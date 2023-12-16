import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import { Race } from '@/Interfaces/Race';
import { raceStateStore } from '@/Stores/raceStateStore';

export const completeRace = (race: Race): void => {
    // raceWeekendStore.completeRace();
    // router.post(route('weekend.race.complete', [ race ]));
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

export const getTotalDisplayValue = (driver: RaceDriver, maxPossibleRng: number): string | number | null => {
    const currentLap = raceStateStore.currentLap;

    if (driver.result.dnf) {
        return driver.result.dnf.toUpperCase();
    }

    if (currentLap === 0) {
        return '-';
    }

    const date = new Date(driver.result.total);

    const hours = date.getUTCHours();
    let minutes = date.getMinutes().toString();
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const millis = date.getMilliseconds().toString().padStart(3, '0');

    if (hours === 0) {
        return `${minutes}:${seconds}.${millis}`;
    } else {
        minutes = minutes.padStart(2, '0');
    }

    return `${hours}:${minutes}:${seconds}.${millis}`;
};
