<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Race"
    />

    <div class="alert bg-danger text-white container w-50" v-if="raceStateStore.showError">
        Something went wrong saving your stints. Please refresh the page and try again.
    </div>

    <RaceButtons
        :can="can"
        @simLap="simNextLap"
        @simRace="simFullRace"
    />

    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ race.order }} - {{ race.name }}</h2>
            <h2 class="ms-auto">
                Race
            </h2>
        </div>
        <div>
            <h2 class="text-center mb-3">
                <span
                    class="rounded-3 px-3"
                    :class="{ 'yellow-flag': flagEventStore.showYellowFlag(), 'green-flag': flagEventStore.showGreenFlag() }"
                >
                    {{ raceStateStore.currentLap }}/{{ raceStateStore.lapDetails.duration }}
                </span>
            </h2>
            <p class="text-center mb-3" v-if="raceStateStore.fastestLapDetails.driver">
                {{ getLaptime(raceStateStore.fastestLapDetails.laptime) }} -
                {{ raceStateStore.fastestLapDetails.driver.full_name }}
            </p>
        </div>
        <table class="table mb-0">
            <thead>
            <tr>
                <th class="text-center">POS</th>
                <th class="text-center">GRID</th>
                <th></th>
                <th class="colour-accent"></th>
                <th>DRIVER</th>
                <th class="text-center"></th>
                <th></th>
                <th>TEAM</th>
                <th class="text-center">LAST</th>
                <th class="text-center">LAST n</th>
                <th class="text-center">TOTAL</th>
                <th class="text-center">LEADER</th>
                <th class="text-center">INTERVAL</th>
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
                <td class="padded-left">
                    <DriverName :firstName="driver.first_name" :lastName="driver.last_name"/>
                </td>
                <td class="smallest-centered fastest-lap">
                    <fa icon="stopwatch" v-if="raceStateStore.fastestLapDetails.driver?.id === driver.id" size="lg"/>
                </td>
                <DriverNumberCell :number="driver.number" :styleString="driver.team.style_string"/>
                <td class="padded-left">{{ driver.team.short_team_name }}</td>
                <td class="medium-centered">{{ getLaptime(driver.result.stints[raceStateStore.currentLap - 1]) }}</td>
                <td class="medium-centered">{{ driver.result.stints.at(-1) }}</td>
                <td class="biggest-centered" :class="getTotalDisplayClasses(driver)">
                    {{ getTotalDisplayValue(driver, maxPossibleRng) }}
                </td>
                <td class="small-centered" :colspan="driver.result.dnf ? 2 : 1">
                    {{ getGapToLeader(drivers, driver) }}
                </td>
                <td class="small-centered" v-if="!driver.result.dnf">
                    {{ getInterval(drivers, driver) }}
                </td>
            </tr>
            </tbody>
        </table>
        <FlagHistory/>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { computed, ComputedRef, onMounted } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Race as RaceInterface } from '@/Interfaces/Race';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import {
    FastestLapConfiguration,
    ReliabilityConfiguration,
    ReliabilityReasons,
} from '@/Interfaces/RaceWeekend/RaceWeekendConfigurations';
import Permission from '@/Interfaces/Permission';
import {
    getPositionChange,
    getPositionChangeIcon,
    getPositionChangeIconClasses,
    getTotalDisplayClasses,
    getTotalDisplayValue,
} from '@/Composables/useRace';
import { getRoll } from '@/Composables/useRng';
import { RaceEventFlag } from '@/Enums/RaceEventFlag';
import FlagHistory from '@/Components/Race/FlagHistory.vue';
import RaceButtons from '@/Components/Race/RaceButtons.vue';
import { raceStateStore } from '@/Stores/raceStateStore';
import { getGapToLeader, getInterval, getLaptime } from '@/Composables/Race/useFormatLaptime';
import { flagEventStore, RaceFlagEvent } from '@/Stores/Race/flagEventStore';
import DriverName from '@/Components/DriverName.vue';
import { YellowFlagType } from '@/Enums/YellowFlagType';

interface Props {
    race: RaceInterface,
    drivers: RaceDriver[],
    fastestLap: FastestLapConfiguration,
    can: Permission,
    reliability_configuration: ReliabilityConfiguration,
    reliability_reasons: ReliabilityReasons,
}

const props = defineProps<Props>();

const minRng = 10;
const maxRng = 40;
let maxPossibleRng = maxRng;

const reliabilityMinRng = props.reliability_configuration.min_rng;
const reliabilityMaxRng = props.reliability_configuration.max_rng;

const yellowFlagTypeChances = {
    [YellowFlagType.SINGLE]: 35,
    [YellowFlagType.DOUBLE]: 35,
    [YellowFlagType.VSC]: 20,
    [YellowFlagType.SC]: 10,
};

// const isLapByLap = props.race.race_type === RaceType.LAP;
// const isTime = props.race.race_type === RaceType.TIME;
// const isDistance = props.race.race_type === RaceType.DISTANCE;

const dnfRollThreshold: ComputedRef<number> = computed(() => {
    // if (raceStateStore.currentLap === 7) {
    //     return -1;
    // }
    //
    // return 100;
    if (raceStateStore.currentLap === 1) {
        return 70;
    }

    if (Math.floor(raceStateStore.currentLap / raceStateStore.lapDetails.duration) > 90) {
        return 70;
    }

    return 90;
});

const shouldRollDnf: ComputedRef<boolean> = computed(() => getRoll(0, 100) > dnfRollThreshold.value);

const getDnfRoll = (driver: RaceDriver): string | null => {
    if (driver.result.dnf) {
        return driver.result.dnf;
    }

    const dnfTypes = [ 'team', 'engine' ];

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

const getTotal = (driver: RaceDriver): number => {
    const total = driver.result.stints.reduce((sum, currentValue) => sum + currentValue, 0);

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

    const ratings = props.drivers.map(driver => driver.ratings.total_rating)
        .sort((a, b) => b - a);

    maxPossibleRng = ratings[0] + maxRng;

    raceStateStore.lapDetails.perPoint = raceStateStore.lapDetails.scale / maxPossibleRng;
};

const sortDriversByPosition = (): void => {
    props.drivers.sort((a, b) => a.result.position - b.result.position);
};

const sortDriversByTotal = (): void => {
    props.drivers.sort((driverOne, driverTwo) => {
        return driverOne.result.total - driverTwo.result.total;
    });
};

const setDriverPositions = (): void => {
    props.drivers.forEach((driver, index) => {
        driver.result.position = index + 1;
        driver.result.position_change = driver.result.starting_position - driver.result.position;
    });
};

const getYellowFlagType = (): YellowFlagType => {
    let chance = getRoll(0, 100);

    for (let [ type, percentage ] of Object.entries(yellowFlagTypeChances)) {
        chance -= percentage;

        if (chance < 0) {
            return <YellowFlagType>type;
        }
    }

    return YellowFlagType.SINGLE;
};

const getExpectedEndLap = (type: YellowFlagType): number => {
    if (type === YellowFlagType.SINGLE || type === YellowFlagType.DOUBLE) {
        return getRoll(1, 3);
    }

    if (type === YellowFlagType.VSC) {
        return getRoll(2, 4);
    }

    if (type === YellowFlagType.SC) {
        return getRoll(3, 6);
    }
};

const simFullRace = (): void => {
    while (raceStateStore.currentLap < raceStateStore.lapDetails.duration) {
        simNextLap();
    }
};

const simNextLap = (): void => {
    raceStateStore.currentLap++;
    let hasDnf = false;
    let lapMinRng = minRng;
    let lapMaxRng = maxRng;
    let overtakingAllowed = true;
    let scale = raceStateStore.lapDetails.scale;
    let margin = raceStateStore.lapDetails.margin;
    let perPoint = scale / maxPossibleRng;

    const lastFlag = flagEventStore.lastFlagEvent();

    props.drivers.forEach((driver, index) => {
        if (driver.result.dnf) {
            return;
        }

        if (shouldRollDnf.value) {
            if (getDnfRoll(driver)) {
                driver.result.total = Number.MAX_SAFE_INTEGER;
                driver.result.dnf_lap = raceStateStore.currentLap;
                hasDnf = true;
                return;
            }
        }

        // const roll = index + 1 * 10;
        const roll = getRoll(minRng, maxRng);
        let score = driver.ratings.total_rating + roll;

        if (raceStateStore.currentLap === 1) {
            score += driver.result.bonus;
        }

        const total = score * perPoint;
        let lapTotal = raceStateStore.lapDetails.baseLaptime + margin + (scale - total);

        if (raceStateStore.currentLap === 1) {
            lapTotal += raceStateStore.lapDetails.firstLapMargin;
        }

        lapTotal = Math.floor(lapTotal);

        if (raceStateStore.isFastestLap(lapTotal)) {
            raceStateStore.setFastestLap(lapTotal, raceStateStore.currentLap, driver);
        }

        driver.result.stints.push(lapTotal);
        driver.result.total += lapTotal;
    });

    sortDriversByTotal();
    setDriverPositions();

    const yellowFlagThreshold = 30;
    const lap = raceStateStore.currentLap;

    if (lastFlag.flag === RaceEventFlag.GREEN) {
        if (hasDnf) {
            // roll for yellow
            if (getRoll(0, 100) > yellowFlagThreshold) {
                const type = getYellowFlagType();

                const event: RaceFlagEvent = {
                    type: type,
                    flag: RaceEventFlag.YELLOW,
                    startLap: lap,
                    expectedEndLap: lap + getExpectedEndLap(type),
                };

                flagEventStore.addFlagEvent(event);

                if (lastFlag) {
                    lastFlag.endLap = lap;
                }
            }
        }
    } else if (lastFlag.flag === RaceEventFlag.YELLOW) {
        // check whether to end yellow
        if (lastFlag.expectedEndLap <= lap) {
            lastFlag.endLap = lap;
        }

        if (lap >= lastFlag.endLap) {
            const event: RaceFlagEvent = {
                flag: RaceEventFlag.GREEN,
                startLap: lap - 1,
            };

            flagEventStore.addFlagEvent(event);
        }
    }
};

onMounted(() => {
    raceStateStore.race = props.race;
    raceStateStore.currentLap = 0;
    raceStateStore.completed = props.race.completed;
    raceStateStore.saving = false;
    raceStateStore.lapDetails = {
        duration: props.race.duration,
        // TODO set real data
        // baseLaptime: 60000,
        // scale: 5000,
        // margin: 2000,
        // firstLapMargin: 5000,
        baseLaptime: getRoll(66000, 106000),
        scale: getRoll(8000, 12000),
        margin: getRoll(2000, 4000),
        firstLapMargin: getRoll(4000, 6000),
        perPoint: maxPossibleRng,
    };
    raceStateStore.setFastestLap(Number.MAX_SAFE_INTEGER, 0, null);

    prepareRace();

    sortDriversByPosition();
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
