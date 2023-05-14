import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import { raceWeekendStore } from '@/Stores/raceWeekendStore.js';
import { router } from '@inertiajs/vue3';
import { Race } from '@/Interfaces/Race';

export const completeRace = (race: Race): void => {
    raceWeekendStore.completeRace();
    router.post(route('weekend.race.complete', [ race ]));
};

export const getPositionChange = (driver: RaceDriver): number => {
    return driver.result.starting_position - driver.result.position;
};

export const getPositionChangeIconClasses = (driver: RaceDriver): string => {
    const positionChange = driver.result.position_change;

    if (positionChange === 0) {
        return 'positions-unchanged';
    }

    if (positionChange > 0) {
        return 'positions-gained';
    }

    return 'positions-lost';
};

export const getPositionChangeIcon = (driver: RaceDriver): string => {
    const positionChange = driver.result.position_change;

    if (positionChange === 0) {
        return 'equals';
    }

    if (positionChange > 0) {
        return 'chevron-up';
    }

    return 'chevron-down';
};

export const getTotalDisplayClasses = (driver: RaceDriver): string => {
    return driver.result.dnf ? 'driver-dnf' : '';
};

export const getTotalDisplayValue = (driver: RaceDriver): string | number | null => {
    if (driver.result.dnf) {
        return driver.result.dnf;
    }

    return driver.result.total;
};
