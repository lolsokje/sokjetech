<template>
    <ActiveRaceWarning v-if="season.has_active_race"/>
    <template v-else-if="teams.length">
        <Errors :errors="form.errors"/>
        <p v-if="state.error" class="text-danger">{{ state.error }}</p>
        <div class="row row-cols-lg-auto mb-3">
            <div class="col-4">
                <input id="teams-min" v-model="state.min" class="form-control" type="number">
            </div>

            <div class="col-4">
                <input id="teams-max" v-model="state.max" class="form-control" type="number">
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
                    <th>Name</th>
                    <th class="text-center">Current</th>
                    <th v-if="inputsHidden" class="text-center">Min</th>
                    <th v-if="inputsHidden" class="text-center">Max</th>
                    <th class="text-center">Dev</th>
                    <th class="text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="team in form.teams" :key="team.id">
                    <BackgroundColourCell :backgroundColour="team.accent_colour"/>
                    <td class="padded-left align-middle">{{ team.name }}</td>
                    <td class="small-centered bg-accent-odd">
                        {{ development.isReliability() ? team.reliability : team.rating }}
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="team.min" class="form-control" type="number">
                    </td>
                    <td v-if="inputsHidden" class="big-centered">
                        <input v-model="team.max" class="form-control" type="number">
                    </td>
                    <td class="small-centered">{{ team.dev }}</td>
                    <td class="bg-accent-even" :class="state.editRatings ? 'medium-centered' : 'small-centered'">
                        <template v-if="!state.editRatings">{{ team.new }}</template>
                        <template v-else>
                            <input type="number" class="form-control" v-model="team.new">
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
    <p v-else>No teams have been added to this season yet</p>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import CopyScreenshotButton from '../Shared/CopyScreenshotButton.vue';
import Development from '../Utilities/Development';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import Errors from '@/Shared/Errors.vue';
import Season from '@/Interfaces/Season';
import DevelopmentState from '@/Interfaces/Development';
import DevelopmentTeam from '@/Interfaces/DevelopmentTeam';

interface Props {
    season: Season,
    teams: DevelopmentTeam[],
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
    teams: props.teams,
});

const development = new Development(props.type);

const applyDevRanges = (): void => {
    state.error = null;
    if (development.validateDevRange(state.min, state.max)) {
        development.applyDevRangesToItems(form.teams, state);
    } else {
        state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
    }
};

const runDev = (): void => {
    state.error = null;
    if (development.performDev(form.teams)) {
        state.completed = true;
    } else {
        state.error = 'One of the teams\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
    }
};

const store = (): void => {
    development.storeDev(form, props.formRoute, state);
};

const devCompleted = computed((): boolean => ! state.completed);
const inputsHidden = computed((): boolean => ! state.hideInputs);
</script>

<script lang="ts">
export default { name: 'TeamDevelopment' };
</script>
