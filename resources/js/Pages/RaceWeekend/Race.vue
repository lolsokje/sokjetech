<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Race"
    />

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your stints. Please refresh the page and try again.
    </div>

    <div class="d-flex mb-3">
        <div class="ms-auto" v-if="can.edit">
            <button v-if="canPerformStint"
                    class="btn btn-primary"
                    @click.prevent="performNextStint()"
                    :disabled="saving"
            >
                Run next stint
            </button>
            <button class="btn btn-primary"
                    v-if="canPerformFastestLap"
                    @click.prevent="fastestLapRoll()"
                    :disabled="saving"
            >
                Fastest lap roll
            </button>
            <button v-if="canCompleteRace"
                    class="btn btn-success"
                    @click.prevent="completeRace(race)"
                    :disabled="saving"
            >
                Complete race
            </button>
        </div>
    </div>

    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ race.order }} - {{ race.name }}</h2>
            <h2 class="ms-auto">
                Race
            </h2>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">POS</th>
                <th class="text-center">GRID</th>
                <th></th>
                <th class="colour-accent"></th>
                <th>DRIVER</th>
                <th class="text-center" v-if="fastestLap.awarded"></th>
                <th></th>
                <th>TEAM</th>
                <th class="text-center">RAT</th>
                <th class="text-center" v-for="stint in race.stints" :key="stint.order">{{ stint.order }}</th>
                <th class="text-center">TOT</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in drivers" :key="driver.id">
                <td class="smallest-centered">{{ driver.result.position }}</td>
                <td class="smallest-centered">{{ driver.result.starting_position }}</td>
                <td class="small-centered" :class="getPositionChangeIconClasses(driver)">
                    <fa :icon="getPositionChangeIcon(driver)"/>
                    <span class="ms-3">{{ Math.abs(driver.result.position_change) }}</span>
                </td>
                <BackgroundColourCell :backgroundColour="driver.team.accent_colour"/>
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="smallest-centered fastest-lap" v-if="fastestLap.awarded">
                    <fa icon="stopwatch" v-if="driver.result.fastest_lap" size="xl"/>
                </td>
                <DriverNumberCell :number="driver.number" :styleString="driver.team.style_string"/>
                <td class="padded-left">{{ driver.team.short_team_name }}</td>
                <td class="small-centered">{{ driver.ratings.total_rating + driver.result.bonus }}</td>
                <td class="small-centered"
                    v-for="(stint, index) in race.stints"
                    :key="stint.order"
                    :class="{ 'bg-accent-odd': !isEven(stint.order)}"
                >
                    {{ driver.result.stints ? driver.result.stints[index] : '' }}
                </td>
                <td class="biggest-centered text-uppercase" :class="getTotalDisplayClasses(driver)">
                    {{ getTotalDisplayValue(driver) }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { computed, ComputedRef, onMounted, ref, Ref } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Race } from '@/Interfaces/Race';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import {
    FastestLapConfiguration,
    ReliabilityConfiguration,
    ReliabilityReasons,
} from '@/Interfaces/RaceWeekend/RaceWeekendConfigurations';
import Permission from '@/Interfaces/Permission';
import { isEven } from '@/Utilities/IsEven.js';
import { getRoll } from '@/Composables/useRng';
import axios from 'axios';
import {
    completeRace,
    getPositionChange,
    getPositionChangeIcon,
    getPositionChangeIconClasses,
    getTotalDisplayClasses,
    getTotalDisplayValue,
} from '@/Composables/useRace';
import { SaveRaceDriver } from '@/ValueObjects/SaveRaceDriver';

interface Props {
    race: Race,
    drivers: RaceDriver[],
    fastestLap: FastestLapConfiguration,
    can: Permission,
    reliability_configuration: ReliabilityConfiguration,
    reliability_reasons: ReliabilityReasons,
}

const props = defineProps<Props>();

const showError: Ref<boolean> = ref(false);
const saving: Ref<boolean> = ref(false);

const currentStint: Ref<number> = ref(props.race.race_details?.current_stint ?? 0);
const fastestLapRollPerformed: Ref<boolean> = ref(props.race?.race_details?.fastest_lap_awarded ?? false);

const reliabilityMinRng = props.reliability_configuration.min_rng;
const reliabilityMaxRng = props.reliability_configuration.max_rng;

const fastestLapRoll = (): void => {
    const drivers = props.drivers.filter(driver => driver.result.dnf === null);
    const values = getFastestLapValues(drivers);

    // https://stackoverflow.com/a/68587921/2466375
    const sorted = values
        .map(Object.entries)
        .sort(([ a ], [ b ]) => b[1] - a[1])
        .map(([ id ]) => id);

    const [ driverId, roll ] = sorted[0];

    const driver = props.drivers.find(d => d.id === driverId);
    driver.result.fastest_lap = true;
    driver.result.fastest_lap_roll = roll;

    if (props.fastestLap.type === 'separate_stint') {
        fastestLapRollPerformed.value = true;

        saveRaceResults();
    }
};

const getFastestLapValues = (drivers: RaceDriver[]): object[] => {
    if (props.fastestLap.type === 'separate_stint') {
        const minRng = props.fastestLap.min_rng;
        const maxRng = props.fastestLap.max_rng;

        return drivers.map(driver => {
            const total = driver.ratings.total_rating + getRoll(minRng, maxRng);
            return { [driver.id]: total };
        });
    } else if (props.fastestLap.type === 'best_last_stint') {
        return drivers.map(driver => {
            return { [driver.id]: driver.result.stints.at(-1) };
        });
    }
};

const performNextStint = (): void => {
    const currentStintSettings = props.race.stints[currentStint.value];

    const minRng = currentStintSettings.min_rng;
    const maxRng = currentStintSettings.max_rng;
    const useDriverRating = currentStintSettings.use_driver_rating;
    const useTeamRating = currentStintSettings.use_team_rating;
    const useEngineRating = currentStintSettings.use_engine_rating;
    const dnfRoll = currentStintSettings.reliability;

    props.drivers.forEach(driver => {
        if (driver.result.dnf) {
            return;
        }

        if (dnfRoll) {
            if (getDnfRoll(driver)) {
                driver.result.total = 0;
                return;
            }
        }

        let total = getRoll(minRng, maxRng);

        if (useDriverRating) {
            total += driver.ratings.driver_rating;
        }

        if (useTeamRating) {
            total += driver.ratings.team_rating;
        }

        if (useEngineRating) {
            total += driver.ratings.engine_rating;
        }

        driver.result.stints[currentStint.value] = total;
        driver.result.total = getTotal(driver);

        driver.result.position_change = getPositionChange(driver);
    });

    sortDriversByTotal();
    setDriverPositions();

    if (shouldRollFastestLapAfterStint()) {
        fastestLapRoll();
    }

    currentStint.value++;

    saveRaceResults();
};

const shouldRollFastestLapAfterStint = (): boolean => {
    if (! props.fastestLap.awarded) {
        return false;
    }

    if (props.fastestLap.type !== 'best_last_stint') {
        return false;
    }

    return currentStint.value === props.race.stints.length - 1;
};

const getDnfRoll = (driver: RaceDriver): string | null => {
    if (driver.result.dnf) {
        return driver.result.dnf;
    }

    const dnfTypes = [ 'team', 'driver', 'engine' ];

    for (let type of dnfTypes) {
        const rating = driver.ratings[`${type}_reliability`];

        if (! rating) {
            return;
        }

        const roll = getRoll(reliabilityMinRng, reliabilityMaxRng);

        if (roll > rating) {
            const reason = getRandomDnfReasonByType(type);
            driver.result.dnf = reason;

            return reason;
        }
    }
};

const getRandomDnfReasonByType = (type: string): string => {
    const reasons = props.reliability_reasons[type];

    return reasons[Math.floor(Math.random() * reasons.length)];
};

const saveRaceResults = (): void => {
    saving.value = true;

    const drivers = props.drivers.map(driver => new SaveRaceDriver(driver));

    const details = {
        current_stint: currentStint.value,
        fastest_lap_awarded: fastestLapRollPerformed.value,
    };

    axios.post(route('weekend.race.store', [ props.race ]), {
        drivers: drivers,
        race_details: details,
    })
        .catch(() => {
            showError.value = true;
            currentStint.value--;
        })
        .finally(() => saving.value = false);
};

const getTotal = (driver: RaceDriver): number => {
    const total = driver.result.stints.reduce((sum, currentValue) => sum + currentValue, driver.ratings.total_rating + getStartingBonus(driver));

    return driver.result.dnf ? 0 : total;
};

const getStartingBonus = (driver: RaceDriver): number => {
    const bonusDecrementBy = 3;
    const maxBonus = props.drivers.length * bonusDecrementBy;

    return maxBonus - (driver.result.starting_position * bonusDecrementBy) + bonusDecrementBy;
};

const prepareRace = (): void => {
    props.drivers.forEach(driver => {
        driver.result.position_change = getPositionChange(driver);
        driver.result.total = getTotal(driver);
        driver.result.bonus = driver.result.bonus ?? getStartingBonus(driver);
    });
};

const sortDriversByPosition = (): void => {
    props.drivers.sort((a, b) => a.result.position - b.result.position);
};

const sortDriversByTotal = (): void => {
    props.drivers.sort((driverOne, driverTwo) => {
        return driverTwo.result.total - driverOne.result.total;
    });
};

const setDriverPositions = (): void => {
    props.drivers.forEach((driver, index) => {
        driver.result.position = index + 1;
        driver.result.position_change = driver.result.starting_position - driver.result.position;
    });
};

const raceCompletionCheck = (): boolean => {
    if (props.race.completed) {
        return false;
    }

    if (! allStintsCompleted.value) {
        return false;
    }

    if (! props.fastestLap.awarded) {
        return true;
    }

    return fastestLapRollPerformed.value;
};

const performNextStintCheck = (): boolean => {
    return ! allStintsCompleted.value && ! showError.value;
};

const fastestLapRollCheck = (): boolean => {
    return allStintsCompleted.value && props.fastestLap.awarded && ! fastestLapRollPerformed.value;
};

const allStintsCompleted: ComputedRef<boolean> = computed(() => currentStint.value === props.race.stints.length);
const canCompleteRace: ComputedRef<boolean> = computed(() => raceCompletionCheck());
const canPerformStint: ComputedRef<boolean> = computed(() => performNextStintCheck());
const canPerformFastestLap: ComputedRef<boolean> = computed(() => fastestLapRollCheck());

onMounted(() => {
    prepareRace();

    sortDriversByPosition();
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
