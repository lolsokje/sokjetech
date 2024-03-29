import { getRoll } from '@/Composables/useRunQualifying';
import { raceStore } from '@/Stores/raceStore';
import axios from 'axios';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';
import { router } from '@inertiajs/vue3';

export const performNextStint = () => {
    const currentStintIndex = raceStore.currentStint;
    const currentStintSettings = raceStore.getCurrentStintDetails(currentStintIndex);
    const minRng = currentStintSettings.min_rng;
    const maxRng = currentStintSettings.max_rng;
    const useDriverRating = currentStintSettings.use_driver_rating;
    const useTeamRating = currentStintSettings.use_team_rating;
    const useEngineRating = currentStintSettings.use_engine_rating;
    const dnfChance = currentStintSettings.reliability;

    raceStore.drivers.forEach(driver => {
        if (driver.result.dnf) {
            return;
        }

        if (dnfChance) {
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

        driver.result.stints[currentStintIndex] = total;
        driver.result.total = getTotal(driver);
    });

    sortDrivers();

    if (raceStore.shouldRollFastestLap() && raceStore.allStintsCompleted()) {
        getFastestLap();
    }

    raceStore.incrementCurrentStint();

    storeRaceResults();
};

export const fastestLapRoll = () => {
    raceStore.drivers.forEach(driver => {
        if (driver.dnf) {
            return driver.result.fastest_lap_roll = null;
        }

        if (! raceStore.fastestLapIsSeparateStint) {
            return driver.result.fastest_lap_roll = driver.result.stints.at(-1);
        } else {
            return driver.result.fastest_lap_roll = driver.ratings.driver_rating + getRoll(raceStore.fastestLapMinRng, raceStore.fastestLapMaxRng);
        }
    });

    getFastestLap();

    raceStore.completeFastestLapRun();

    storeRaceResults();
};

export const completeRace = () => {
    raceStore.setRaceCompleted(true);
    raceWeekendStore.completeRace();
    router.post(route('weekend.race.complete', [ raceStore.raceId ]));
};

export const getTotal = (driver) => {
    const total = driver.result.stints.reduce((sum, currentValue) => sum + currentValue, driver.ratings.total_rating + driver.result.bonus);

    return driver.result.dnf ? 0 : total;
};

export const sortDrivers = () => {
    raceStore.drivers.sort((driverOne, driverTwo) => {
        if (driverOne.result.stints.length === 0 && ! driverOne.result.dnf) {
            return driverOne.result.starting_position - driverTwo.result.starting_position;
        }
        return driverTwo.result.total - driverOne.result.total;
    });

    raceStore.drivers.forEach((driver, index) => {
        driver.result.position = index + 1;
        driver.result.position_change = getPositionChange(driver);
    });
};

export const calculateDriverTotals = () => {
    raceStore.drivers.forEach(driver => {
        driver.result.position_change = getPositionChange(driver);
        driver.result.total = getTotal(driver);
        driver.result.bonus = driver.result.bonus ?? getStartingBonus(driver);
    });
};

const getStartingBonus = (driver) => {
    const bonusDecrementBy = 3;
    const maxBonus = raceStore.drivers.length * bonusDecrementBy;

    return maxBonus - (driver.result.starting_position * bonusDecrementBy) + bonusDecrementBy;
};

export const getPositionChange = (driver) => {
    return driver.result.starting_position - driver.result.position;
};

const getDnfRoll = (driver) => {
    if (driver.result.dnf) {
        return driver.result.dnf;
    }

    const ratings = driver.ratings;
    const teamRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);
    const engineRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);
    const driverRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);

    if (teamRoll > ratings.team_reliability && ratings.team_reliability > 0) {
        return driver.result.dnf = raceStore.getRandomReliabilityReason('team');
    }

    if (engineRoll > ratings.engine_reliability && ratings.engine_reliability > 0) {
        return driver.result.dnf = raceStore.getRandomReliabilityReason('engine');
    }

    if (driverRoll > ratings.driver_reliability && ratings.driver_reliability > 0) {
        return driver.result.dnf = raceStore.getRandomReliabilityReason('driver');
    }
    return false;
};

const getFastestLap = () => {
    const drivers = raceStore.drivers.slice();

    drivers.sort((driverOne, driverTwo) => driverTwo.result.fastest_lap_roll - driverOne.result.fastest_lap_roll);

    // first item in array will be the fastest lap
    const highestRoll = drivers[0].result.fastest_lap_roll;

    // if the second item in the array has a lower fastest_lap_roll, award the fastest lap to first driver
    if (drivers[1].result.fastest_lap_roll < highestRoll) {
        raceStore.drivers.find(d => d.id === drivers[0].id).result.fastest_lap = true;
    }
};

const storeRaceResults = () => {
    raceStore.saving = true;
    const drivers = [];

    raceStore.drivers.forEach(driver => {
        drivers.push({
            id: driver.id,
            entrant_id: driver.entrant_id,
            stints: driver.result.stints,
            position: driver.result.position,
            starting_bonus: driver.result.bonus,
            driver_rating: driver.ratings.driver_rating,
            team_rating: driver.ratings.team_rating,
            engine_rating: driver.ratings.engine_rating,
            dnf: driver.result.dnf,
            fastest_lap_roll: driver.result.fastest_lap_roll,
            fastest_lap: driver.result.fastest_lap,
        });
    });

    const raceDetails = {
        current_stint: raceStore.currentStint,
        fastest_lap_awarded: raceStore.fastestLapRunCompleted,
    };

    axios.post(route('weekend.race.store', [ raceStore.raceId ]), { drivers, race_details: raceDetails })
        .catch(() => raceStore.setShowError(true))
        .finally(() => raceStore.saving = false);
};
