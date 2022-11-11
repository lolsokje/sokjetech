<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="alert bg-danger text-white container w-50" v-if="raceStore.error">
        Something went wrong saving your stints. Please refresh the page and try again.
    </div>

    <div class="d-flex mb-3">
        <h3>Race</h3>
        <div class="ms-auto" v-if="can.edit">
            <button v-if="canPerformStint"
                    class="btn btn-primary"
                    @click.prevent="performNextStint()"
                    :disabled="raceStore.saving"
            >
                Run next stint
            </button>
            <button class="btn btn-primary"
                    v-if="canPerformFastestLap"
                    @click.prevent="fastestLapRoll()"
                    :disabled="raceStore.saving"
            >
                Fastest lap roll
            </button>
            <button v-if="canCompleteRace"
                    class="btn btn-success"
                    @click.prevent="completeRace()"
                    :disabled="raceStore.saving"
            >
                Complete race
            </button>
        </div>
    </div>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="text-center">GRID</th>
            <th></th>
            <th class="colour-accent"></th>
            <th class="colour-accent"></th>
            <th>DRIVER</th>
            <th></th>
            <th>TEAM</th>
            <th class="text-center">RAT</th>
            <th class="text-center">BON</th>
            <th class="text-center" v-for="stint in race.stints" :key="stint.order">{{ stint.order }}</th>
            <th class="text-center">TOT</th>
            <th class="text-center" v-if="raceStore.fastestLapIsAwarded">FL</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="smallest-centered">{{ driver.result.position }}</td>
            <td class="smallest-centered">{{ driver.result.starting_position }}</td>
            <td class="smallest-centered" :class="getPositionChangeDisplayClasses(driver)">
                {{ getPositionChange(driver) }}
            </td>
            <td class="colour-accent"></td>
            <BackgroundColourCell :backgroundColour="driver.team.accent_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="smallest-centered" :style="driver.team.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team.short_team_name }}</td>
            <td class="small-centered bg-accent-even">{{ driver.ratings.total_rating }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.result.bonus }}</td>
            <td class="small-centered"
                v-for="(stint, index) in race.stints"
                :key="stint.order"
                :class="{ 'bg-accent-even': isEven(index) }"
            >
                {{ driver.result.stints ? driver.result.stints[index] : '' }}
            </td>
            <td class="biggest-centered text-uppercase" :class="getTotalDisplayClasses(driver)">
                {{ getTotalDisplayValue(driver) }}
            </td>
            <td class="small-centered" v-if="raceStore.fastestLapIsAwarded" :class="getFastestLapClass(driver)">
                {{ driver.result.fastest_lap_roll }}
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import { computed, onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import { isEven } from '@/Utilities/IsEven';
import { raceStore } from '@/Stores/raceStore';
import {
    calculateDriverTotals,
    completeRace,
    fastestLapRoll,
    getPositionChange,
    performNextStint,
    sortDrivers,
} from '@/Composables/useRunRace';

const props = defineProps({
    race: Object,
    drivers: Array,
    fastestLap: Object,
    can: Object,
    reliability_configuration: Object,
    reliability_reasons: Object,
});

const reliabilityMinRng = props.reliability_configuration.min_rng;
const reliabilityMaxRng = props.reliability_configuration.max_rng;

const getFastestLapClass = (driver) => {
    return driver.fastest_lap ? 'bg-purple' : '';
};

const getPositionChangeDisplayClasses = (driver) => {
    if (driver.result.position_change === undefined || driver.result.position_change === 0) {
        return 'bg-warning';
    }

    if (driver.result.position_change < 0) {
        return 'bg-danger';
    }

    return 'bg-success';
};

const getTotalDisplayClasses = (driver) => {
    return driver.result.dnf ? 'position-dnf' : '';
};

const getTotalDisplayValue = (driver) => {
    if (driver.result.dnf) {
        return driver.result.dnf;
    }

    return driver.result.total;
};

const canCompleteRace = computed(() => raceStore.canCompleteRace());
const canPerformStint = computed(() => raceStore.canPerformNextStint());
const canPerformFastestLap = computed(() => raceStore.canPerformFastestLapRoll());

onMounted(() => {
    raceStore.setDrivers(props.drivers);
    calculateDriverTotals();
    raceStore.setCurrentStint(props.race.race_details?.current_stint ?? 0);

    if (raceStore.currentStint > 0) {
        sortDriversByPosition(props.drivers);
    } else {
        sortDrivers();
    }

    raceStore.setRaceId(props.race.id);
    raceStore.setRaceCompleted(props.race.completed);
    raceStore.setFastestLapDetails(props.fastestLap);
    raceStore.setStintCount(props.race.stints.length);
    raceStore.setFastestLapRunCompleted(props.race.race_details?.fastest_lap_awarded ?? false);
    raceStore.setStintDetails(props.race.stints);
    raceStore.setReliabilityRng(reliabilityMinRng, reliabilityMaxRng);
    raceStore.setReliabilityReasons(props.reliability_reasons);
});
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
