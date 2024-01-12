<template>
    <div class="mb-4 pb-3 race-button-divider">
        <div class="alert alert-danger text-center" v-if="error">
            Something went wrong saving the race result, please try again
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <Breadcrumb :link="route('seasons.races.index', race.season)"
                            :linkText="race.season.full_name"
                            :label="race.name"
                            append="Race"
                            withoutMargin
                />
            </div>
            <div v-if="raceInProgress && canEdit">
                <button class="btn btn-outline-warning me-3"
                        v-if="maxLapsToSimulate > 1"
                        @click.prevent="simulateLaps(maxLapsToSimulate)"
                        :disabled="buttonsDisabled"
                >
                    Sim next {{ maxLapsToSimulate }} laps
                </button>
                <button class="btn btn-secondary me-3" @click.prevent="simulateNextLap()" :disabled="buttonsDisabled">
                    Sim next lap
                </button>
                <button class="btn btn-danger" @click.prevent="simulateRace();" :disabled="buttonsDisabled">
                    Sim race
                </button>
            </div>
            <div v-else-if="!race.completed">
                <button class="btn btn-success" @click.prevent="completeRace" :disabled="buttonsDisabled">
                    Complete race
                </button>
            </div>
        </div>
    </div>
    <div id="screenshot-target">
        <div class="d-flex justify-content-between align-items-center text-center mb-3">
            <div>
                <h3 class="text-uppercase">
                    Round {{ race.order }} -{{ race.name }}
                </h3>
            </div>
            <div class="lap-counter">
                {{ currentLap }} / {{ race.duration }}
            </div>
            <div class="text-uppercase">
                <h3>
                    {{ race.circuit.name }}
                </h3>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">POS</th>
                <th class="text-center">GRID</th>
                <th></th>
                <th class="colour-accent"></th>
                <th>DRIVER</th>
                <th></th>
                <th>TEAM</th>
                <th class="text-center">TOTAL</th>
                <th class="text-center">LEADER</th>
                <th class="text-center">INTERVAL</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="result in results" :key="result.id">
                <td class="smallest-centered">{{ result.performance.position }}</td>
                <td class="smallest-centered">{{ result.performance.starting_position }}</td>
                <td class="small-centered" :class="getPositionChangeIconClasses(result.performance.position_change)">
                    <fa :icon="getPositionChangeIcon(result.performance.position_change)"></fa>
                    <span class="ms-3">{{ Math.abs(result.performance.position_change) }}</span>
                </td>
                <BackgroundColourCell :backgroundColour="result.team.accent_colour"/>
                <td class="padded-left">
                    <DriverName :firstName="result.driver.first_name" :lastName="result.driver.last_name"/>
                </td>
                <DriverNumberCell :number="result.driver.number" :styleString="result.team.style_string"/>
                <td class="padded-left">{{ result.team.name }}</td>
                <td class="medium-centered">{{ result.performance.race_total }}</td>
                <td class="medium-centered">
                    <span v-if="currentLap > 0">{{ getGapToLeader(result) }}</span>
                    <span v-else>-</span>
                </td>
                <td class="medium-centered">
                    <span v-if="currentLap > 0">{{ getInterval(result) }}</span>
                    <span v-else>-</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { Race } from '@/Interfaces/Race';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import DriverName from '@/Components/DriverName.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import RaceResult from '@/Interfaces/Race/RaceResult';
import { computed, ComputedRef, onMounted, ref, Ref } from 'vue';
import { getRoll } from '@/Composables/useRng';
import { getPositionChange, getPositionChangeIcon, getPositionChangeIconClasses } from '@/Composables/useRace';
import axios from 'axios';
import { router, usePage } from '@inertiajs/vue3';
import route from 'ziggy-js';
import Breadcrumb from '@/Components/Breadcrumb.vue';

type Props = {
    race: Race,
    results: RaceResult[],
}

const props = defineProps<Props>();

const saving: Ref<boolean> = ref(false);
const error: Ref<boolean> = ref(false);

const canEdit = usePage().props.can.edit;

const buttonsDisabled: ComputedRef<boolean> = computed(() => saving.value || error.value);

const laps = props.race.duration;
const currentLap: Ref<number> = ref(props.race.current_lap);
const raceInProgress: ComputedRef<boolean> = computed(() => currentLap.value < laps);
const maxLapsToSimulate: ComputedRef<number> = computed(() => Math.min(5, laps - currentLap.value));

const minRng = 5;
const maxRng = 30;

const simulateNextLap = (): void => {
    simulateLap();

    saveResults();
};

const simulateRace = (): void => {
    while (currentLap.value < laps) {
        simulateLap();
    }

    saveResults();
};

const simulateLaps = (lapsToSimulate: number): void => {
    const targetLap = Math.min(currentLap.value + lapsToSimulate, laps);

    while (currentLap.value < targetLap) {
        simulateLap();
    }

    saveResults();
};

const simulateLap = (): void => {
    props.results.forEach(result => {
        const roll = getRoll(minRng, maxRng);

        result.performance.stints.push(roll);

        result.performance.stints_total += roll;
        result.performance.race_total += roll;
    });

    props.results.sort((a, b) => b.performance.race_total - a.performance.race_total);

    props.results.forEach((result, index) => {
        result.performance.position = index + 1;
        result.performance.position_change = getPositionChange(result.performance.starting_position, result.performance.position);
    });

    currentLap.value++;
};

const saveResults = (): void => {
    saving.value = true;
    const results = [];

    props.results.forEach(result => {
        results.push({
            id: result.id,
            position: result.performance.position,
            stints: result.performance.stints,
            total: result.performance.race_total,
        });
    });

    const details = {
        current_lap: currentLap.value,
        results,
    };

    axios.put(route('weekend.race.results.update', props.race), details)
        .catch(() => {
            error.value = true;
        })
        .finally(() => saving.value = false);
};

const completeRace = (): void => {
    saving.value = true;
    router.post(route('weekend.race.complete', props.race));
};

const getGapToLeader = (driver: RaceResult): string | number => {
    const leaderTotal = leader.value.performance.race_total;
    const driverTotal = driver.performance.race_total;

    if (leaderTotal === driverTotal) {
        return '-';
    }

    return driverTotal - leaderTotal;
};

const getInterval = (driver: RaceResult): string | number => {
    if (driver.performance.position === 1) {
        return '-';
    }

    const driverAhead = props.results.find(d => d.performance.position === driver.performance.position - 1);

    if (! driverAhead) {
        return '-';
    }

    const interval = driver.performance.race_total - driverAhead.performance.race_total;

    return interval === 0 ? '-' : interval;
};

const leader: ComputedRef<RaceResult> = computed(() => props.results.find(r => r.performance.position === 1) ?? props.results[0]);

onMounted(() => {
    if (currentLap.value === 0) {
        props.results.sort((a, b) => a.performance.starting_position - b.performance.starting_position);
    } else {
        props.results.sort((a, b) => a.performance.position - b.performance.position);
    }
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
