<template>
    <h3>Qualifying - Q{{ store.getCurrentSessionIndex() + 1 }}</h3>
    <div class="d-flex my-3">
        <button class="btn btn-info" @click="viewPreviousSession()" v-if="canViewPreviousSession">
            Previous session
        </button>
        <div class="ms-auto">
            <div v-if="canRunQualifying">
                <button @click.prevent="performRun()" class="btn btn-primary" v-if="canPerformRun">
                    Perform Run
                </button>
            </div>
            <button @click.prevent="viewNextSession()" v-if="!canPerformRun && canContinueToNextSession"
                    class="btn btn-secondary">
                Next session
            </button>
            <button @click.prevent="completeQualifying()" class="btn btn-success" v-if="canCompleteQualifying">
                Complete qualifying
            </button>
        </div>
    </div>

    <table class="table table-bordered table-dark" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">Pos</th>
            <th class="colour-accent"></th>
            <th>Driver</th>
            <th class="text-center">#</th>
            <th>Team</th>
            <th class="text-center">Rating</th>
            <th v-for="i in 3" :key="i" class="text-center">Run {{ i }}</th>
            <th class="text-center">Best</th>
            <th class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(driver, position) in drivers" :key="driver.id">
            <tr v-if="canDriverParticipateInCurrentSession(position)">
                <td class="text-center" :class="isDriverBelowSessionCutoff(position + 1) ? 'bg-danger' : ''">
                    {{ position + 1 }}
                </td>
                <td class="colour-accent" :style="`background-color: ${driver.primary_colour}`"></td>
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
                <td class="padded-left">{{ driver.team_name }}</td>
                <td class="text-center">{{ driver.total_rating }}</td>
                <td v-for="i in 3" :key="i" class="text-center">
                    {{ driver.runs ? driver.runs[store.getCurrentSessionIndex()][i - 1] : '' }}
                </td>
                <td class="text-center">{{ driver.best_stint }}</td>
                <td class="text-center">{{ driver.total }}</td>
            </tr>
        </template>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import {
    calculateSessionBestAndTotal,
    fillDriverRuns,
    performQualifyingRun,
    sortDriversByPosition,
    sortDriversByTotal,
} from '@/Composables/useRunQualifying';
import { threeSessionEliminationStore as store } from '@/Stores/threeSessionEliminationStore';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';

const props = defineProps({
    formatDetails: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
    canRunQualifying: {
        type: Boolean,
        required: true,
    },
    results: {
        type: Object,
        required: false,
    },
    sessionDetails: {
        type: Object,
        required: false,
    },
    completed: {
        type: Boolean,
        required: false,
    },
    showError: Boolean,
});

const emit = defineEmits([ 'runPerformed', 'completeQualifying' ]);

const totalSessions = 3;

const maxDrivers = {
    1: props.formatDetails.q2_driver_count,
    2: props.formatDetails.q3_driver_count,
};

const performRun = () => {
    performQualifyingRun(store, canDriverParticipateInCurrentSession);
    store.incrementCompletedRunsForCurrentSession();

    emit('runPerformed', {
        results: store.getDrivers(),
        details: {
            completed_runs: store.getCompletedRunsPerSession(),
            current_session: store.getCurrentSessionIndex(),
        },
    });
};

const viewPreviousSession = () => {
    store.decrementCurrentSession();

    store.getDrivers().forEach(driver => {
        calculateSessionBestAndTotal(driver, store.getCurrentSessionIndex());
    });
    sortDriversByTotal(store.getDrivers());
};

const viewNextSession = () => {
    store.incrementCurrentSession();

    store.getDrivers().forEach(driver => {
        if (!driver.runs[store.getCurrentSessionIndex()]) {
            driver.runs[store.getCurrentSessionIndex()] = [];
        }
        calculateSessionBestAndTotal(driver, store.getCurrentSessionIndex());
    });

    if (store.getCurrentSessionRunCount() === 0) {
        sortDriversByPosition(store.getDrivers());
    } else {
        sortDriversByTotal(store.getDrivers());
    }
};

const completeQualifying = () => {
    emit('completeQualifying');
    store.resetQualifying();
};

const canDriverParticipateInCurrentSession = (position) => {
    if (store.getCurrentSessionIndex() === 0) {
        return true;
    }

    const maxDriversInCurrentSession = maxDrivers[store.getCurrentSessionIndex()];

    return position < maxDriversInCurrentSession;
};

const isDriverBelowSessionCutoff = (position) => {
    const maxDriversInNextSession = maxDrivers[store.getCurrentSessionNumber()];

    return position > maxDriversInNextSession;
};

const hasError = computed(() => props.showError === true);
const canPerformRun = computed(() => (store.getCurrentSessionRunCount() < props.formatDetails.runs_per_session) && !hasError.value);
const canContinueToNextSession = computed(() => (store.getCurrentSessionNumber() < totalSessions) && !hasError.value);
const canViewPreviousSession = computed(() => store.getCurrentSessionIndex() > 0);
const canCompleteQualifying = computed(() => store.getCurrentSessionNumber() === 3 && store.getCurrentSessionRunCount() === 3 && !props.completed);

onMounted(() => {
    store.setDrivers(props.drivers);

    if (props.sessionDetails) {
        store.setCompletedRunsPerSession(props.sessionDetails.completed_runs);
        store.setCurrentSession(props.sessionDetails.current_session);
    }

    store.setMinRng(props.formatDetails.min_rng);
    store.setMaxRng(props.formatDetails.max_rng);

    fillDriverRuns(store.getDrivers(), store.getCurrentSessionIndex(), props.results);
});
</script>

<script>
export default {
    name: 'ThreeSessionEliminationQualifying',
};
</script>
