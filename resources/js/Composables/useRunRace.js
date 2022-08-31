import { getRoll } from '@/Composables/useRunQualifying';
import { raceStore } from '@/Stores/raceStore';
import axios from 'axios';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';
import { Inertia } from '@inertiajs/inertia';

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

    if (raceStore.shouldRollFastestLap() && raceStore.allStintsCompleted()) {
        getFastestLap();
    }

    raceStore.incrementCurrentStint();

    storeRaceResults();
};

export const fastestLapRoll = () => {
    raceStore.drivers.forEach(driver => {
        if (driver.dnf) {
            return driver.fastest_lap_roll = null;
        }

        if (!raceStore.fastestLapIsSeparateStint) {
            return driver.fastest_lap_roll = driver.stints.at(-1);
        } else {
            return driver.fastest_lap_roll = driver.driver_rating + getRoll(raceStore.fastestLapMinRng, raceStore.fastestLapMaxRng);
        }
    });

    getFastestLap();

    raceStore.completeFastestLapRun();

    storeRaceResults();
};

export const completeRace = () => {
    raceStore.setRaceCompleted(true);
    raceWeekendStore.completeRace();
    Inertia.post(route('weekend.race.complete', [ raceStore.raceId ]));
};

export const getTotal = (driver) => {
    const total = driver.stints.reduce((sum, currentValue) => sum + currentValue, driver.total_rating + driver.bonus);

    return driver.dnf ? 0 : total;
};

export const sortDrivers = () => {
    raceStore.drivers.sort((driverOne, driverTwo) => {
        if (driverOne.stints.length === 0 && !driverOne.dnf) {
            return driverOne.starting_position - driverTwo.starting_position;
        }
        return driverTwo.total - driverOne.total;
    });

    raceStore.drivers.forEach((driver, index) => {
        driver.position = index + 1;
        driver.position_change = getPositionChange(driver);
    });
};

export const mergeRaceResults = (results) => {
    results.forEach(result => {
        const driver = raceStore.drivers.find(d => d.id === result.driver_id);
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
    });
};

const getStartingBonus = (driver) => {
    const bonusDecrementBy = 3;
    const maxBonus = raceStore.drivers.length * bonusDecrementBy;

    return maxBonus - (driver.starting_position * bonusDecrementBy) + bonusDecrementBy;
};

export const getPositionChange = (driver) => {
    return driver.starting_position - driver.position;
};

const getDnfRoll = (driver) => {
    if (driver.dnf) {
        return driver.dnf;
    }

    const teamRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);
    const engineRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);
    const driverRoll = getRoll(raceStore.reliabilityMinRng, raceStore.reliabilityMaxRng);

    if (teamRoll > driver.team_reliability && driver.team_reliability > 0) {
        return driver.dnf = raceStore.getRandomReliabilityReason('team');
    }

    if (engineRoll > driver.engine_reliability && driver.engine_reliability > 0) {
        return driver.dnf = raceStore.getRandomReliabilityReason('engine');
    }

    if (driverRoll > driver.driver_reliability && driver.driver_reliability > 0) {
        return driver.dnf = raceStore.getRandomReliabilityReason('driver');
    }
    return false;
};

const getFastestLap = () => {
    const drivers = raceStore.drivers.slice();

    drivers.sort((driverOne, driverTwo) => driverTwo.fastest_lap_roll - driverOne.fastest_lap_roll);

    // first item in array will be the fastest lap
    const highestRoll = drivers[0].fastest_lap_roll;

    // if the second item in the array has a lower fastest_lap_roll, award the fastest lap to first driver
    if (drivers[1].fastest_lap_roll < highestRoll) {
        raceStore.drivers.find(d => d.id === drivers[0].id).fastest_lap = true;
    }
};

const storeRaceResults = () => {
    raceStore.saving = true;
    const drivers = [];

    raceStore.drivers.forEach(driver => {
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

    const raceDetails = {
        current_stint: raceStore.currentStint,
        fastest_lap_awarded: raceStore.fastestLapRunCompleted,
    };

    axios.post(route('weekend.race.store', [ raceStore.raceId ]), { drivers, race_details: raceDetails })
        .catch(() => raceStore.setShowError(true))
        .finally(() => raceStore.saving = false);
};
