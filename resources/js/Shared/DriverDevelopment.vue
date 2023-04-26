<template>
    <ActiveRaceWarning v-if="season.has_active_race"/>
    <template v-else-if="drivers.length">
        <Errors :errors="form.errors"/>
        <p v-if="state.error" class="text-danger">{{ state.error }}</p>
        <div class="row row-cols-lg-auto mb-3">
            <div class="col-4">
                <input id="drivers-min" v-model="state.min" class="form-control" type="number">
            </div>

            <div class="col-4">
                <input id="drivers-max" v-model="state.max" class="form-control" type="number">
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
                    <input type="checkbox" id="edit-ratings" v-model="state.editRatings" class="form-check-inline">
                    <label for="edit-ratings" class="form-check-label">Edit ratings directly?</label>
                </div>
            </div>
        </div>

        <form @submit.prevent="store">
            <table id="screenshot-target" class="table">
                <thead>
                <tr>
                    <th class="colour-accent"></th>
                    <th>Driver</th>
                    <th class="text-center">#</th>
                    <th>Team</th>
                    <th class="text-center">Age</th>
                    <th class="text-center">Current</th>
                    <th v-if="inputsHidden" class="text-center">Min</th>
                    <th v-if="inputsHidden" class="text-center">Max</th>
                    <th class="text-center">Dev</th>
                    <th class="text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="driver in form.drivers" :key="driver.id">
                    <BackgroundColourCell :backgroundColour="driver.accent_colour"/>
                    <td class="padded-left align-middle">{{ driver.full_name }}</td>
                    <td :style="driver.team_style" class="small-centered">{{ driver.number }}</td>
                    <td class="padded-left align-middle">{{ driver.team_name }}</td>
                    <td class="small-centered">{{ driver.age }}</td>
                    <td class="small-centered bg-accent-odd">
                        {{ development.isReliability() ? driver.reliability : driver.rating }}
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="driver.min" class="form-control" type="number">
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="driver.max" class="form-control" type="number">
                    </td>
                    <td class="small-centered">{{ driver.dev }}</td>
                    <td class="bg-accent-even" :class="state.editRatings ? 'medium-centered' : 'small-centered'">
                        <template v-if="!state.editRatings">{{ driver.new }}</template>
                        <template v-else>
                            <input type="number" class="form-control" v-model="driver.new">
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="d-flex">
                <CopyScreenshotButton/>
                <button :disabled="devCompleted && !state.editRatings" class="btn btn-primary ms-auto" type="submit">
                    Save dev
                </button>
            </div>
        </form>
    </template>
    <p v-else>No drivers have been added to any entrants yet</p>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CopyScreenshotButton from './CopyScreenshotButton.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import Errors from '@/Shared/Errors.vue';
import DevelopmentState from '@/Interfaces/Development';
import Season from '@/Interfaces/Season';
import Development from '@/Utilities/Development';
import DevelopmentDriver from '@/Interfaces/DevelopmentDriver';

interface Props {
    season: Season,
    type?: string,
    formRoute: string,
    drivers: DevelopmentDriver[],
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
    drivers: props.drivers,
});

const development = new Development(props.type);

const applyDevRanges = (): void => {
    state.error = null;
    if (development.validateDevRange(state.min, state.max)) {
        development.applyDevRangesToItems(form.drivers, state);
    } else {
        state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
    }
};

const runDev = (): void => {
    state.error = null;
    if (development.performDev(form.drivers)) {
        state.completed = true;
    } else {
        state.error = 'One of the drivers\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
    }
};

const store = (): void => {
    development.storeDev(form, props.formRoute, state);
};

const devCompleted = computed((): boolean => ! state.completed);
const inputsHidden = computed((): boolean => ! state.hideInputs);

onMounted(() => {
    form.drivers.sort((a, b) => a.team_name.localeCompare(b.team_name));
});
</script>

<script lang="ts">
export default { name: 'DriverDevelopment' };
</script>
