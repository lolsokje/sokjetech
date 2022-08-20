<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your stints. Please refresh the page and try again.
    </div>

    <div class="d-flex mb-3">
        <h3>Race</h3>
        <div class="ms-auto" v-if="can.edit">
            <button v-if="canPerformStint" class="btn btn-primary" @click.prevent="performNextStint()">
                Run next stint
            </button>
            <button class="btn btn-primary" v-if="canPerformFastestLap" @click.prevent="fastestLapRoll()">
                Fastest lap roll
            </button>
            <button v-if="canCompleteRace" class="btn btn-success" @click.prevent="completeRace()">
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
            <th class="text-center" v-if="awardFastestLapPoint">FL</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="small-centered">{{ driver.position }}</td>
            <td class="small-centered">{{ driver.starting_position }}</td>
            <td class="small-centered" :class="getPositionChangeDisplayClasses(driver)">
                {{ getPositionChange(driver) }}
            </td>
            <td class="colour-accent"></td>
            <BackgroundColourCell :backgroundColour="driver.primary_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.short_team_name }}</td>
            <td class="small-centered bg-accent-even">{{ driver.total_rating }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.bonus }}</td>
            <td class="small-centered"
                v-for="(stint, index) in race.stints"
                :key="stint.order"
                :class="{ 'bg-accent-even': isEven(index) }"
            >
                {{ driver.stints ? driver.stints[index] : '' }}
            </td>
            <td class="biggest-centered text-uppercase" :class="getTotalDisplayClasses(driver)">
                {{ getTotalDisplayValue(driver) }}
            </td>
            <td class="small-centered" v-if="awardFastestLapPoint" :class="getFastestLapClass(driver)">
                {{ driver.fastest_lap_roll }}
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { computed, onMounted, ref } from 'vue';
import { getRoll, sortDriversByPosition } from '@/Composables/useRunQualifying';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import BackgroundColourCell from '@/Components/BackgroundColourCell';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';
import { isEven } from '@/Utilities/IsEven';

const props = defineProps({
    race: Object,
    drivers: Array,
    raceResults: Array,
    fastestLap: Object,
    can: Object,
    reliability_configuration: Object,
    reliability_reasons: Object,
});

const showError = ref(false);
const currentStint = ref(0);

const bonusDecrementBy = 3;
const driverCount = props.drivers.length;

const awardFastestLapPoint = props.fastestLap.awarded;
const fastestLapIsSeparateStint = props.fastestLap.type === 'separate_stint';
const fastestLapMinRng = props.fastestLap.min_rng;
const fastestLapMaxRng = props.fastestLap.max_rng;
const fastestLapRunCompleted = ref(!(awardFastestLapPoint && fastestLapIsSeparateStint));

const reliabilityMinRng = props.reliability_configuration.min_rng;
const reliabilityMaxRng = props.reliability_configuration.max_rng;
const dnfReasons = props.reliability_reasons;
const teamDnfReasons = dnfReasons.team;
const engineDnfReasons = dnfReasons.engine;
const driverDnfReasons = dnfReasons.driver;

const performNextStint = () => {
    const currentStintIndex = currentStint.value;
    const currentStintSettings = props.race.stints[currentStintIndex];
    const minRng = currentStintSettings.min_rng;
    const maxRng = currentStintSettings.max_rng;
    const useDriverRating = currentStintSettings.use_driver_rating;
    const useTeamRating = currentStintSettings.use_team_rating;
    const useEngineRating = currentStintSettings.use_engine_rating;
    const dnfChance = currentStintSettings.reliability;

    props.drivers.forEach(driver => {
        if (driver.dnf) {
            return;
        }

        if (dnfChance) {
            if (getDnfRoll(driver)) {
                driver.total = 0;
                return;
            }
        }

        let total = getRoll(minRng, maxRng);

        if (useDriverRating) {
            total += driver.driver_rating;
        }

        if (useTeamRating) {
            total += driver.team_rating;
        }

        if (useEngineRating) {
            total += driver.engine_rating;
        }

        driver.stints[currentStintIndex] = total;
        driver.total = getTotal(driver);
    });

    sortDrivers();

    if (awardFastestLapPoint && !fastestLapIsSeparateStint && currentStint.value === props.race.stints.length - 1) {
        getFastestLap();
    }

    storeRaceResults();

    currentStint.value++;
};

const storeRaceResults = () => {
    const drivers = [];

    props.drivers.forEach(driver => {
        drivers.push({
            id: driver.id,
            entrant_id: driver.entrant_id,
            stints: driver.stints,
            position: driver.position,
            starting_bonus: driver.bonus,
            driver_rating: driver.driver_rating,
            team_rating: driver.team_rating,
            engine_rating: driver.engine_rating,
            dnf: driver.dnf,
            fastest_lap_roll: driver.fastest_lap_roll,
            fastest_lap: driver.fastest_lap,
        });
    });

    axios.post(route('weekend.race.store', [ props.race ]), { drivers: drivers })
        .catch(() => showError.value = true);
};

const completeRace = () => {
    raceWeekendStore.completeRace();
    Inertia.post(route('weekend.race.complete', [ props.race ]));
};

const fastestLapRoll = () => {
    props.drivers.forEach(driver => {
        if (driver.dnf) {
            return driver.fastest_lap_roll = null;
        }

        return driver.fastest_lap_roll = driver.driver_rating + getRoll(fastestLapMinRng, fastestLapMaxRng);
    });

    getFastestLap();

    fastestLapRunCompleted.value = true;

    if (fastestLapIsSeparateStint) {
        storeRaceResults();
    }
};

const getFastestLap = () => {
    const drivers = props.drivers.slice();
    if (!fastestLapIsSeparateStint) {
        const currentStintIndex = currentStint.value;
        drivers.forEach(driver => driver.fastest_lap_roll = driver.driver_rating + driver.stints[currentStintIndex]);
    }

    drivers.sort((driverOne, driverTwo) => driverTwo.fastest_lap_roll - driverOne.fastest_lap_roll);

    const highestRoll = drivers[0].fastest_lap_roll;

    if (drivers[1].fastest_lap_roll < highestRoll) {
        props.drivers.find(d => d.id === drivers[0].id).fastest_lap = true;
    }
};

const getFastestLapClass = (driver) => {
    return driver.fastest_lap ? 'bg-purple' : '';
};

const getDnfRoll = (driver) => {
    if (driver.dnf) {
        return driver.dnf;
    }

    const teamRoll = getRoll(reliabilityMinRng, reliabilityMaxRng);
    const engineRoll = getRoll(reliabilityMinRng, reliabilityMaxRng);
    const driverRoll = getRoll(reliabilityMinRng, reliabilityMaxRng);

    if (teamRoll > driver.team_reliability) {
        return driver.dnf = getDnfReason(teamDnfReasons);
    }

    if (engineRoll > driver.engine_reliability) {
        return driver.dnf = getDnfReason(engineDnfReasons);
    }

    if (driverRoll > driver.driver_reliability) {
        return driver.dnf = getDnfReason(driverDnfReasons);
    }
    return false;
};

const getDnfReason = (reasons) => {
    return reasons[Math.floor(Math.random() * reasons.length)];
};

const mergeRaceResults = () => {
    let stintCount = 0;
    props.raceResults.forEach(result => {
        const driver = props.drivers.find(d => d.id === result.driver_id);
        driver.stints = result.stints;
        driver.position = result.position;
        driver.starting_position = result.starting_position;
        driver.driver_rating = result.driver_rating;
        driver.team_rating = result.team_rating;
        driver.engine_rating = result.engine_rating;
        driver.total_rating = driver.driver_rating + driver.team_rating + driver.engine_rating;
        driver.position_change = getPositionChange(driver);
        driver.bonus = result.starting_bonus ?? getStartingBonus(driver);
        driver.dnf = result.dnf;
        driver.fastest_lap_roll = result.fastest_lap_roll ?? null;
        driver.fastest_lap = result.fastest_lap ?? false;
        driver.total = getTotal(driver);

        if (driver.stints.length > stintCount) {
            stintCount = driver.stints.length;
        }
    });

    currentStint.value = stintCount;
};

const getPositionChange = (driver) => {
    return driver.starting_position - driver.position;
};

const getPositionChangeDisplayClasses = (driver) => {
    if (driver.position_change === undefined || driver.position_change === 0) {
        return 'bg-warning';
    }

    if (driver.position_change < 0) {
        return 'bg-danger';
    }

    return 'bg-success';
};

const getStartingBonus = (driver) => {
    const maxBonus = driverCount * bonusDecrementBy;

    return maxBonus - (driver.starting_position * bonusDecrementBy) + bonusDecrementBy;
};

const getTotal = (driver) => {
    const total = driver.stints.reduce((sum, currentValue) => sum + currentValue, driver.total_rating + driver.bonus);

    return driver.dnf ? 0 : total;
};

const getTotalDisplayClasses = (driver) => {
    return driver.dnf ? 'bg-danger' : '';
};

const getTotalDisplayValue = (driver) => {
    if (driver.dnf) {
        return driver.dnf;
    }

    return driver.total;
};

const sortDrivers = () => {
    props.drivers.sort((driverOne, driverTwo) => {
        if (driverOne.stints.length === 0 && !driverOne.dnf) {
            return driverOne.starting_position - driverTwo.starting_position;
        }
        return driverTwo.total - driverOne.total;
    });

    props.drivers.forEach((driver, index) => {
        driver.position = index + 1;
        driver.position_change = getPositionChange(driver);
    });
};

const allStintsCompleted = computed(() => currentStint.value === props.race.stints.length);
const canCompleteRace = computed(() => allStintsCompleted.value && !props.race.completed && fastestLapRunCompleted.value === true);
const canPerformStint = computed(() => !allStintsCompleted.value && showError.value === false);
const canPerformFastestLap = computed(() => {
    const fastestLapRunPerformed = fastestLapRunCompleted.value === true;

    return allStintsCompleted.value === true && awardFastestLapPoint && fastestLapIsSeparateStint && !fastestLapRunPerformed;
});

onMounted(() => {
    mergeRaceResults();

    if (currentStint.value > 0) {
        sortDriversByPosition(props.drivers);
    } else {
        sortDrivers();
    }

    fastestLapRunCompleted.value = props.raceResults.length && props.raceResults?.some(r => r.fastest_lap);
});
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
