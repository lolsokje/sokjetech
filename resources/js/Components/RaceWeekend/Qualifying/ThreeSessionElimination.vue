<template>
    <div class="d-flex my-3">
        <button v-if="canViewPreviousSession"
                class="btn btn-info"
                @click.prevent="viewPreviousSession()"
                :disabled="qualifyingStore.saving || qualifyingStore.error"
        >
            Previous session
        </button>

        <div class="ms-auto" v-if="qualifyingStore.canRunQualifying">
            <button @click.prevent="performRun()"
                    class="btn btn-primary"
                    :disabled="qualifyingStore.saving || qualifyingStore.error"
                    v-if="canPerformRun()"
            >
                Perform run
            </button>
            <button v-if="canCompleteQualifying()"
                    class="btn btn-success"
                    @click.prevent="completeQualifying()"
                    :disabled="qualifyingStore.saving || qualifyingStore.error"
            >
                Complete qualifying
            </button>
        </div>

        <button v-if="canViewNextSession"
                class="btn btn-secondary"
                @click.prevent="viewNextSession()"
                :disabled="qualifyingStore.saving || qualifyingStore.error"
        >
            Next session
        </button>
    </div>
    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ qualifyingStore.race.order }} - {{ qualifyingStore.race.name }}</h2>
            <h2 class="ms-auto">Qualifying</h2>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Pos</th>
                <th class="colour-accent"></th>
                <th>Driver</th>
                <th class="text-center">#</th>
                <th>Team</th>
                <th class="text-center">Rating</th>
                <th v-for="i in qualifyingStore.formatDetails.runs_per_session" :key="i" class="text-center">
                    {{ i }}
                </th>
                <th class="text-center">Best</th>
                <th class="text-center">Total</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(result, position) in qualifyingStore.activeDrivers" :key="result.id">
                <td class="smallest-centered" :class="{ 'bg-danger': isDriverBelowSessionCutoff(position + 1) }">
                    {{ position + 1 }}
                </td>
                <BackgroundColourCell :backgroundColour="result.team.accent_colour"/>
                <td>
                    <DriverName :firstName="result.driver.first_name" :lastName="result.driver.last_name"/>
                </td>
                <DriverNumberCell :number="result.driver.number" :styleString="result.team.style_string"/>
                <td class="padded-left">{{ result.team.name }}</td>
                <td class="small-centered bg-accent-odd">{{ result.ratings.total }}</td>
                <td v-for="i in qualifyingStore.formatDetails.runs_per_session"
                    :key="i"
                    class="small-centered"
                    :class="{ 'bg-accent-even': isEven(i) }"
                >
                    {{ result.performance.sessions[qualifyingStore.currentSession - 1]?.runs[i - 1] ?? '' }}
                </td>
                <td class="small-centered">{{ result.performance.best_stint }}</td>
                <td class="small-centered bg-accent-odd">{{ result.performance.total }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts" setup>
import { computed, ComputedRef } from 'vue';
import { qualifyingStore } from '@/Stores/qualifyingStore';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { isEven } from '@/Utilities/IsEven';
import DriverName from '@/Components/DriverName.vue';
import {
    canCompleteQualifying,
    canPerformRun,
    completeQualifying,
    getBestRuns,
    performRun,
    sortDriversByPosition,
} from '@/Composables/useQualifying';

const driverCountPerSession = computed(() => {
    return {
        1: qualifyingStore.results.length,
        2: qualifyingStore.formatDetails?.q2_driver_count,
        3: qualifyingStore.formatDetails?.q3_driver_count,
    };
});

const maxDriversNextSession: ComputedRef<number> = computed(() => driverCountPerSession.value[qualifyingStore.currentSession + 1]);

const canViewPreviousSession = computed(() => qualifyingStore.currentSession > 1);

const canViewNextSession = computed(() => {
    return (qualifyingStore.currentSession < qualifyingStore.totalSessions) && qualifyingStore.currentRun > qualifyingStore.formatDetails.runs_per_session;
});

const viewPreviousSession = (): void => {
    qualifyingStore.currentSession--;
    setDriversForSession();

    qualifyingStore.currentRun = qualifyingStore.formatDetails.runs_per_session + 1;

    getBestRuns();
    sortDriversByPosition();
};

const viewNextSession = (): void => {
    qualifyingStore.currentSession++;
    setDriversForSession();

    qualifyingStore.currentRun = qualifyingStore.activeDrivers[0].performance.sessions[qualifyingStore.currentSession - 1].runs.length + 1;

    getBestRuns();
    sortDriversByPosition();
};

const setDriversForSession = (): void => {
    const sessionIndex = qualifyingStore.currentSession - 1;
    qualifyingStore.activeDrivers = [];

    const cutoff = driverCountPerSession.value[qualifyingStore.currentSession];

    qualifyingStore.results.forEach(driver => {
        let previousSession = driver.performance.sessions[sessionIndex - 1];

        // If the current session isn't the first session, and either no result for the previous session exists
        // or the previous session position is below the cutoff, don't add the current driver to active drivers
        if (sessionIndex > 0 && (previousSession === undefined || previousSession.position > cutoff)) {
            return;
        }

        // If no data exists for the current driver in the current session, initialize it
        if (driver.performance.sessions[sessionIndex] === undefined) {
            driver.performance.sessions[sessionIndex] = {
                position: previousSession.position,
                runs: [],
            };
        }

        qualifyingStore.activeDrivers.push(driver);
    });
};

const isDriverBelowSessionCutoff = (position: number): boolean => {
    return position > maxDriversNextSession.value;
};

const setActiveDrivers = (): void => {
    setDriversForSession();
};

defineExpose({
    setActiveDrivers,
});
</script>
