<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your stints. Please refresh the page and try again.
    </div>

    <div class="d-flex mb-3">
        <h3>Race</h3>
        <div class="ms-auto">
            <button v-if="canPerformStint" class="btn btn-primary" @click.prevent="performNextStint()">
                Run next stint
            </button>
            <button v-if="canCompleteRace" class="btn btn-success" @click.prevent="completeRace()">
                Complete race
            </button>
        </div>
    </div>

    <table class="table table-dark table-bordered">
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
            <td class="colour-accent" :style="`background-color: ${driver.primary_colour}`"></td>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.short_team_name }}</td>
            <td class="small-centered">{{ driver.total_rating }}</td>
            <td class="small-centered">{{ driver.bonus }}</td>
            <td class="small-centered" v-for="(stint, index) in race.stints" :key="stint.order">
                {{ driver.stints ? driver.stints[index] : '' }}
            </td>
            <td class="text-center text-uppercase" :class="getTotalDisplayClasses(driver)">
                {{ getTotalDisplayValue(driver) }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { computed, onMounted, ref } from 'vue';
import { getRoll, sortDriversByPosition } from '@/Composables/useRunQualifying';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    race: Object,
    drivers: Array,
    raceResults: Array,
});

const showError = ref(false);
const currentStint = ref(0);

const bonusDecrementBy = 3;
const driverCount = props.drivers.length;

const teamDnfReasons = [
    'suspension',
    'crash',
    'spin',
];

const engineDnfReasons = [
    'oil leak',
    'gearbox',
];

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
    currentStint.value++;

    storeRaceResults();
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
        });
    });

    axios.post(route('weekend.race.store', [ props.race ]), { drivers: drivers })
        .catch(() => showError.value = true);
};

const completeRace = () => {
    Inertia.post(route('weekend.race.complete', [ props.race ]));
};

const getDnfRoll = (driver) => {
    if (driver.dnf) {
        return driver.dnf;
    }

    const teamRoll = getRoll(1, 100);
    const engineRoll = getRoll(1, 100);

    if (teamRoll > driver.team_reliability) {
        return driver.dnf = getDnfReason(teamDnfReasons);
    }

    if (engineRoll > driver.engine_reliability) {
        return driver.dnf = getDnfReason(engineDnfReasons);
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

const raceCompleted = computed(() => currentStint.value === props.race.stints.length);
const canCompleteRace = computed(() => raceCompleted.value && !props.race.completed);
const canPerformStint = computed(() => !raceCompleted.value && showError.value === false);

onMounted(() => {
    mergeRaceResults();

    if (currentStint.value > 0) {
        sortDriversByPosition(props.drivers);
    } else {
        sortDrivers();
    }
});
</script>

<script>
import RaceWeekend from '@/Shared/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
