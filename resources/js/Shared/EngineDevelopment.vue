<template>
    <ActiveRaceWarning v-if="season.has_active_race"/>
    <template v-else-if="engines.length">
        <Errors :errors="form.errors"/>
        <p v-if="state.error" class="text-danger">{{ state.error }}</p>

        <div class="row row-cols-lg-auto mb-3">
            <div class="col-4">
                <input id="engines-min" v-model="state.min" class="form-control" type="number">
            </div>

            <div class="col-4">
                <input id="engines-max" v-model="state.max" class="form-control" type="number">
            </div>
            <button class="btn btn-primary" @click.prevent="applyDevRanges">Apply</button>

            <div class="ms-auto">
                <button :disabled="!devCompleted" class="btn btn-success" @click.prevent="runDev">
                    Run dev
                </button>
            </div>
        </div>

        <div class="mb-3">
            <div>
                <div class="form-check-inline">
                    <input id="edit-mode" v-model="state.hideInputs" class="form-check-inline" type="checkbox">
                    <label class="form-check-label" for="edit-mode">Hide inputs</label>
                </div>
            </div>

            <p class="mb-2">
                Rebadged engines without individual ratings will take the engine rating from their base engine, so no
                min/max RNG inputs will be shown. You also won't be able to edit their ratings directly.
            </p>

            <div>
                <div class="form-check-inline">
                    <input type="checkbox" v-model="state.editRatings" class="form-check-inline" id="edit-ratings">
                    <label for="edit-ratings" class="form-check-label">Edit ratings directly</label>
                </div>
            </div>
        </div>

        <form @submit.prevent="store">
            <table id="screenshot-target" class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center">Rebadged</th>
                    <th class="text-center">Individual rating</th>
                    <th class="text-center">Current</th>
                    <th v-if="inputsHidden" class="text-center">Min</th>
                    <th v-if="inputsHidden" class="text-center">Max</th>
                    <th class="text-center">Dev</th>
                    <th class="text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="engine in form.engines" :key="engine.id">
                    <td class="padded-left">{{ engine.name }}</td>
                    <td class="big-centered">{{ engine.rebadge ? 'Yes' : 'No' }}</td>
                    <td class="big-centered">
                        <template v-if="engine.rebadge">
                            {{ engine.individual_rating ? 'Yes' : 'No' }}
                        </template>
                    </td>
                    <td class="small-centered bg-accent-odd">
                        {{ development.isReliability() ? engine.reliability : engine.rating }}
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="engine.min" class="form-control" type="number"
                               v-if="showEngineInput(engine)"
                        >
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="engine.max" class="form-control" type="number"
                               v-if="showEngineInput(engine)"
                        >
                    </td>
                    <td class="small-centered">{{ engine.dev }}</td>
                    <td class="bg-accent-even" :class="state.editRatings ? 'medium-centered' : 'small-centered'">
                        <template v-if="!state.editRatings">
                            {{ engine.new }}
                        </template>
                        <template v-else>
                            <template v-if="showEngineInput(engine)">
                                <input type="number" class="form-control" v-model="engine.new">
                            </template>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="d-flex">
                <CopyScreenshotButton/>
                <button :disabled="!state.editRatings && devCompleted" class="btn btn-primary ms-auto" type="submit">
                    Save dev
                </button>
            </div>
        </form>
    </template>
    <p v-else>No engines have been added yet</p>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Development from '@/Utilities/Development';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import Errors from '@/Shared/Errors.vue';
import Season from '@/Interfaces/Season';
import DevelopmentState from '@/Interfaces/Development';
import DevelopmentEngine from '@/Interfaces/DevelopmentEngine';

interface Props {
    season: Season,
    engines: DevelopmentEngine[],
    type?: string,
    formRoute: string,
}

const props = defineProps<Props>();

const state: DevelopmentState = reactive({
    error: null,
    completed: false,
    hideInputs: false,
    min: 0,
    max: 0,
    editRatings: false,
});

const form = useForm({
    engines: props.engines,
});

const development = new Development(props.type);

const applyDevRanges = (): void => {
    state.error = null;
    if (development.validateDevRange(state.min, state.max)) {
        development.applyDevRangesToItems(form.engines, state);
    } else {
        state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
    }
};

const runDev = (): void => {
    state.error = null;
    if (development.performDev(form.engines)) {
        updateEnginesUsingParentRating();
        state.completed = true;
    } else {
        state.error = 'One of the engines\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
    }
};

const updateEnginesUsingParentRating = (): void => {
    form.engines.filter(e => ! e.individual_rating).forEach(engine => {
        const parentEngine = form.engines.find(e => e.base_engine_id === engine.base_engine_id && ! e.rebadge);

        if (! parentEngine) {
            return;
        }

        engine.dev = parentEngine.dev;
        engine.new = parentEngine.new;
    });
};

const store = (): void => {
    development.storeDev(form, props.formRoute, state);
};

const showEngineInput = (engine): boolean => {
    return ! engine.rebadge || engine.rebadge && engine.individual_rating;
};

const devCompleted = computed((): boolean => ! state.completed);
const inputsHidden = computed((): boolean => ! state.hideInputs);

onMounted(() => {
    form.engines.sort((a, b) => a.name.localeCompare(b.name));
});
</script>

<script lang="ts">
export default { name: "EngineDevelopment" };
</script>
