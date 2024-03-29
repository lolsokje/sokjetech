<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Qualifying"
    />

    <div class="d-flex my-3" v-if="canRunQualifying">
        <div class="ms-auto">
            <button @click.prevent="performRun()" class="btn btn-primary" v-if="canPerformRun" :disabled="saving">
                Perform run
            </button>
            <button @click.prevent="completeQualifying()"
                    class="btn btn-success"
                    v-if="canCompleteQualifying"
                    :disabled="saving"
            >
                Complete qualifying
            </button>
        </div>
    </div>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">Pos</th>
            <th class="colour-accent"></th>
            <th>Driver</th>
            <th class="text-center">#</th>
            <th>Team</th>
            <th class="text-center">Rating</th>
            <th v-for="i in formatDetails.runs_per_session" :key="i" class="text-center">{{ i }}</th>
            <th class="text-center">Best</th>
            <th class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(driver, position) in drivers" :key="driver.id">
            <td class="smallest-centered">
                {{ position + 1 }}
            </td>
            <BackgroundColourCell :backgroundColour="driver.team.accent_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="smallest-centered" :style="driver.team.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team.team_name }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.ratings.total_rating }}</td>
            <td v-for="i in formatDetails.runs_per_session"
                :key="i"
                class="small-centered"
                :class="{ 'bg-accent-even': isEven(i) }"
            >
                {{ driver.result.runs.length ? driver.result.runs[store.getCurrentSessionIndex()][i - 1] : '' }}
            </td>
            <td class="small-centered">{{ driver.result.best_stint }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.result.total }}</td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import { singleSessionStore as store } from '@/Stores/singleSessionStore';
import { calculateDriverTotals, performQualifyingRun } from '@/Composables/useRunQualifying';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { isEven } from '@/Utilities/IsEven';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    formatDetails: Object,
    drivers: Array,
    canRunQualifying: Boolean,
    results: Object,
    sessionDetails: Object,
    race: Object,
    completed: Boolean,
    showError: Boolean,
    saving: Boolean,
});

const emit = defineEmits([ 'runPerformed', 'completeQualifying' ]);

const performRun = () => {
    performQualifyingRun(store);
    store.incrementCurrentRun();

    emit('runPerformed', {
        results: store.getDrivers(),
        details: {
            completed_runs: store.getCurrentRun(),
        },
    });
};

const completeQualifying = () => {
    emit('completeQualifying');
};

const hasError = computed(() => props.showError === true);
const canPerformRun = computed(() => (store.getCurrentSessionRunCount() < props.formatDetails.runs_per_session) && ! hasError.value);
const canCompleteQualifying = computed(() => store.getCurrentSessionRunCount() === props.formatDetails.runs_per_session && ! props.completed);

onMounted(() => {
    store.setDrivers(props.drivers);

    store.setMinRng(props.formatDetails.min_rng);
    store.setMaxRng(props.formatDetails.max_rng);

    if (props.sessionDetails) {
        store.setCurrentRun(props.sessionDetails.completed_runs);
    }

    calculateDriverTotals(store.getDrivers(), store.getCurrentSessionIndex(), props.results);
});

onBeforeUnmount(() => store.resetQualifyingSessionStats());
</script>

<script>
export default { name: 'SingleSession' };
</script>
