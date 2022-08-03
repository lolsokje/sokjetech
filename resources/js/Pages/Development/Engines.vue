<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Engine development</h2>

    <ActiveRaceWarning v-if="season.has_active_race"/>
    <template v-else-if="engines.length">
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

            <div>
                <div class="form-check-inline">
                    <input type="checkbox" v-model="state.editRatings" class="form-check-inline" id="edit-ratings">
                    <label for="edit-ratings" class="form-check-label">Edit ratings directly</label>
                </div>
            </div>
        </div>

        <form @submit.prevent="store">
            <table id="screenshot-target" class="table table-bordered table-dark">
                <thead>
                <tr>
                    <th>Name</th>
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
                    <td class="big-centered">{{ engine.individual_rating ? 'Yes' : 'No' }}</td>
                    <td class="small-centered">{{ engine.rating }}</td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="engine.min" class="form-control" type="number"
                               v-if="engine.individual_rating">
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="engine.max" class="form-control" type="number"
                               v-if="engine.individual_rating">
                    </td>
                    <td class="small-centered">{{ engine.dev }}</td>
                    <td class="small-centered">
                        <template v-if="!state.editRatings">
                            {{ engine.new }}
                        </template>
                        <template v-else>
                            <input type="number" class="form-control" v-model="engine.new">
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

<script setup>
import { computed, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Development from '@/Utilities/Development';
import DevelopmentEngine from '@/Utilities/DevelopmentEngine';
import BackLink from '@/Shared/BackLink';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    engines: {
        type: Array,
        required: true,
    },
});

const state = reactive({
    error: null,
    completed: false,
    hideInputs: false,
    min: 0,
    max: 0,
    editRatings: false,
});

const form = useForm({
    engines: [],
});

function applyDevRanges () {
    state.error = null;
    if (Development.validateDevRange(state)) {
        Development.applyDevRangesToItems(form.engines, state);
    } else {
        state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
    }
}

function runDev () {
    state.error = null;
    if (Development.performDev(form.engines)) {
        updateEnginesUsingParentRating();
        state.completed = true;
    } else {
        state.error = 'One of the engines\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
    }
}

function updateEnginesUsingParentRating () {
    form.engines.filter(e => e.individual_rating === false).forEach(engine => {
        const parentEngine = form.engines.find(e => e.base_engine_id === engine.base_engine_id && e.rebadge === false);
        if (!parentEngine) {
            return;
        }

        engine.dev = parentEngine.dev;
        engine.new = parentEngine.new;
    });
}

function store () {
    Development.storeDev(form, route('seasons.development.engines.store', [ props.season ]), state);
}

const devCompleted = computed(() => !state.completed);
const inputsHidden = computed(() => !state.hideInputs);

onMounted(() => {
    props.engines.forEach((engine) => {
        form.engines.push(new DevelopmentEngine(engine));
    });

    form.engines.sort((a, b) => a.name.localeCompare(b.name));
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
