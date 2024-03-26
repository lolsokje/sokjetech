import QualifyingResult from '@/Interfaces/Race/QualifyingResult';
import { raceWeekendStore } from '@/Stores/raceWeekendStore.js';
import { qualifyingStore } from '@/Stores/qualifyingStore';
import { router } from '@inertiajs/vue3';
import { getRoll } from '@/Composables/useRandom';
import axios from 'axios';

export const sortDriversByPosition = (): void => {
    const session = qualifyingStore.currentSession - 1;

    qualifyingStore.activeDrivers.sort((a, b) => a.performance.sessions[session].position - b.performance.sessions[session].position);
};

export const sortDriversBySessionTotal = (): void => {
    qualifyingStore.activeDrivers.sort((a, b) => b.performance.total - a.performance.total);
};

export const setDriverPositions = (): void => {
    const session = qualifyingStore.currentSession - 1;

    qualifyingStore.activeDrivers.forEach((driver, index: number) => {
        driver.performance.sessions[session].position = index + 1;
    });
};

export const canPerformRun = (): boolean => {
    return qualifyingStore.currentRun <= qualifyingStore.formatDetails.runs_per_session;
};

export const performRun = (): void => {
    const currentSessionNumber = qualifyingStore.currentSession - 1;
    const currentRunNumber = qualifyingStore.currentRun - 1;
    const min = qualifyingStore.formatDetails.min_rng;
    const max = qualifyingStore.formatDetails.max_rng;

    qualifyingStore.activeDrivers.forEach(driver => {
        const driverResult = qualifyingStore.results.find(d => d.id === driver.id);

        if (! driverResult) {
            return;
        }

        const roll = getRoll(min, max);
        driver.performance.sessions[currentSessionNumber].runs[currentRunNumber] = roll;
        driverResult.performance.sessions[currentSessionNumber].runs[currentRunNumber] = roll;
    });

    getBestRuns();
    sortDriversBySessionTotal();
    setDriverPositions();
    storeQualifyingRunResult();

    qualifyingStore.currentRun++;
};

export const canCompleteQualifying = (): boolean => {
    if (qualifyingStore.currentSession < qualifyingStore.totalSessions) {
        return false;
    }

    return qualifyingStore.currentRun > qualifyingStore.formatDetails.runs_per_session && ! qualifyingStore.race.qualifying_completed;
};

export const getBestRuns = (): void => {
    qualifyingStore.activeDrivers.forEach(driver => {
        const bestRun = getBestRun(driver);

        driver.performance.best_stint = bestRun;
        driver.performance.total = driver.ratings.total + bestRun;
    });
};

const getBestRun = (driver: QualifyingResult): number | null => {
    const runs = driver.performance.sessions[qualifyingStore.currentSession - 1]?.runs;

    if (! runs || ! runs.length) {
        return null;
    }

    return Math.max(...runs);
};

export const completeQualifying = (): void => {
    raceWeekendStore.completeQualifying();

    router.post(route('weekend.qualifying.complete', [ qualifyingStore.race ]));
};

export const storeQualifyingRunResult = (): void => {
    qualifyingStore.saving = true;

    const sessionDetails = {
        current_session: qualifyingStore.currentSession,
        current_run: qualifyingStore.currentRun + 1,
    };

    const driversToSave = qualifyingStore.activeDrivers.map(driver => {
        driver.performance.position = driver.performance.sessions[qualifyingStore.currentSession - 1].position;

        return driver;
    });

    axios.post(route('weekend.qualifying.results.store', [ qualifyingStore.race ]), {
        drivers: driversToSave,
        details: sessionDetails,
    })
        .catch(() => qualifyingStore.error = true)
        .finally(() => qualifyingStore.saving = false);
};
