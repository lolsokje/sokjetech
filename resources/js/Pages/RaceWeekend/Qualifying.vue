<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Qualifying"
    />

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your runs. Please refresh the page and try again.
    </div>

    <div class="d-flex my-3">
        <button v-if="canViewPreviousSession && isMultiSession"
                class="btn btn-info"
                @click.prevent="viewPreviousSession()"
                :disabled="saving"
        >
            Previous session
        </button>
        <div class="ms-auto" v-if="canRunQualifying">
            <button @click.prevent="performRun()" class="btn btn-primary" :disabled="saving" v-if="canPerformRun">
                Perform run
            </button>
            <button v-if="canCompleteQualifying"
                    class="btn btn-success"
                    @click.prevent="completeQualifying()"
                    :disabled="saving"
            >
                Complete qualifying
            </button>
        </div>

        <button v-if="canViewNextSession && isMultiSession"
                class="btn btn-secondary"
                @click.prevent="viewNextSession()"
                :disabled="saving"
        >
            Next session
        </button>
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
        <tr v-for="(driver, position) in driversInSession" :key="driver.id">
            <td class="smallest-centered" :class="isDriverBelowSessionCutoff(position) ? 'bg-danger' : ''">
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
                {{ driver.result.sessions ? driver.result.sessions[currentSession].runs[i - 1] : '' }}
            </td>
            <td class="small-centered">{{ driver.result.best_stint }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.result.total }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import { Race } from '@/Interfaces/Race';
import { computed, ComputedRef, onMounted, Ref, ref } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { isEven } from '@/Utilities/IsEven.js';
import FormatDetails from '@/Interfaces/RaceWeekend/FormatDetails';
import QualifyingDriver from '@/Interfaces/RaceWeekend/QualifyingDriver';
import { getRoll } from '@/Composables/useRng';
import {
    setDriverPositions,
    sortDriversByPosition,
    sortDriversBySessionTotal,
    sortDriversByTotal,
} from '@/Composables/useQualifying';
import Permission from '@/Interfaces/Permission';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { raceWeekendStore } from '@/Stores/raceWeekendStore.js';

interface SessionDrivers {
    [key: number]: QualifyingDriver[];
}

interface Props {
    race: Race,
    drivers: QualifyingDriver[],
    sessions: number,
    can: Permission,
}

interface SessionDrivers {
    [key: number]: QualifyingDriver[],
}

const props = defineProps<Props>();

const showError: Ref<boolean> = ref(false);
const saving: Ref<boolean> = ref(false);
const formatDetails: Ref<FormatDetails> = ref(props.race.season.format);

const currentSession: Ref<number> = ref(props.race.qualifying_details?.current_session ?? 1);
const isMultiSession = props.sessions > 1;

const canRunQualifying = props.can.edit;
const canViewPreviousSession = computed(() => currentSession.value > 1);
const canViewNextSession = computed(() => {
    return (currentSession.value < props.sessions) && currentRun.value === formatDetails.value.runs_per_session;
});

const currentRun: Ref<number> = ref(0);
const runsPerSession: Ref<number> = ref(formatDetails.value.runs_per_session);

const driversPerSession: Ref<SessionDrivers> = ref({
    1: props.drivers,
});

const driverCountPerSession = {
    1: props.drivers.length,
    2: formatDetails.value?.q2_driver_count,
    3: formatDetails.value?.q3_driver_count,
};

const driversInSession: ComputedRef<QualifyingDriver[]> = computed(() => driversPerSession.value[currentSession.value]);

const minRng: number = formatDetails.value.min_rng;
const maxRng: number = formatDetails.value.max_rng;

const viewPreviousSession = (): void => {
    setDriversForSession(currentSession.value, currentSession.value - 1);
    currentSession.value--;
    currentRun.value = formatDetails.value.runs_per_session;

    getBestRuns(driversInSession.value);
    sortDriversByPosition(driversInSession.value, currentSession.value);
};

const viewNextSession = (): void => {
    setDriversForSession(currentSession.value, currentSession.value + 1);
    currentSession.value++;
    setSessionCurrentRunNumber();

    getBestRuns(driversInSession.value);
    sortDriversByPosition(driversInSession.value, currentSession.value);
};

const setSessionCurrentRunNumber = (): void => {
    currentRun.value = driversInSession.value[0].result.sessions[currentSession.value].runs.length;
};

const setDriversForSession = (currentSessionNumber: number, newSessionNumber: number): void => {
    if (driversPerSession.value[newSessionNumber] !== undefined) {
        return;
    }

    const newDrivers = [];

    for (let driver of driversInSession.value) {
        const position = driver.result.sessions[currentSessionNumber].position;
        if (position > driverCountPerSession[newSessionNumber]) {
            continue;
        }

        if (driver.result.sessions[newSessionNumber] === undefined) {
            driver.result.sessions[newSessionNumber] = {
                position,
                runs: [],
            };
        }

        newDrivers.push(driver);
    }

    driversPerSession.value[newSessionNumber] = newDrivers;
};

const performRun = (): void => {
    const currentSessionNumber = currentSession.value;
    const currentRunNumber = currentRun.value;

    driversInSession.value.forEach(driver => {
        if (! driver.result.sessions) {
            driver.result.sessions = {
                [currentSessionNumber]: {
                    runs: [],
                },
            };
        }

        driver.result.sessions[currentSessionNumber].runs[currentRunNumber] = getRoll(minRng, maxRng);
    });

    getBestRuns(driversInSession.value);
    sortDriversBySessionTotal(driversInSession.value);
    setDriverPositions(driversInSession.value, currentSession.value);
    storeQualifyingRunResult();

    currentRun.value++;
};

const getBestRuns = (drivers: QualifyingDriver[]): void => {
    drivers.forEach(driver => {
        const bestRun = getBestRun(driver);

        driver.result.best_stint = bestRun;
        driver.result.total = driver.ratings.total_rating + bestRun;
    });
};

const getBestRun = (driver: QualifyingDriver): number | null => {
    const runs = driver.result.sessions[currentSession.value].runs;

    if (! runs.length) {
        return null;
    }

    return Math.max(...runs);
};

const canPerformRun: ComputedRef<boolean> = computed(() => currentRun.value < runsPerSession.value);
const canCompleteQualifying: ComputedRef<boolean> = computed(() => {
    if (currentSession.value < props.sessions) {
        return false;
    }

    return currentRun.value === runsPerSession.value && ! props.race.qualifying_completed;
});

const isDriverBelowSessionCutoff = (position: number): boolean => {
    if (props.sessions < 2) {
        return false;
    }

    const maxDriversInNextSession = driverCountPerSession[currentSession.value + 1];

    return position >= maxDriversInNextSession;
};

const setDriversForAllSessions = (): void => {
    driversPerSession.value = {};

    props.drivers.forEach(driver => {
        for (let [ session, runs ] of Object.entries(driver.result.sessions)) {
            if (driversPerSession.value[session] === undefined) {
                driversPerSession.value[session] = [];
            }

            if (runs.runs?.length) {
                driversPerSession.value[session].push(driver);
            }
        }
    });
};

const storeQualifyingRunResult = (): void => {
    saving.value = true;

    const sessionDetails = {
        current_session: currentSession.value,
    };

    const drivers = driversPerSession.value;
    const driversToSave: QualifyingDriver[] = [];

    const sessionNumber = currentSession.value;
    const driversInCurrentSession = drivers[sessionNumber];

    driversInCurrentSession.forEach(driver => {
        driver.result.position = driver.result.sessions[sessionNumber].position;

        driversToSave.push(driver);
    });

    axios.post(route('weekend.qualifying.results.store', [ props.race ]), {
        drivers: driversToSave,
        details: sessionDetails,
    })
        .catch(() => showError.value = true)
        .finally(() => saving.value = false);
};

const completeQualifying = (): void => {
    raceWeekendStore.completeQualifying();
    router.post(route('weekend.qualifying.complete', [ props.race ]));
};

onMounted(() => {
    if (props.race.qualifying_started) {
        setDriversForAllSessions();
        setSessionCurrentRunNumber();
        getBestRuns(driversInSession.value);

        sortDriversByPosition(driversInSession.value, currentSession.value);
    } else {
        sortDriversByTotal(driversInSession.value);
    }
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
