import { raceStateStore } from '@/Stores/raceStateStore';
import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export const getLaptime = (number: number | undefined): string => {
    if (number === undefined) {
        return '';
    }

    return numberToLaptime(number);
};

export const numberToLaptime = (number: number): string => {
    const date = new Date(number);
    const minutes = date.getMinutes().toString();
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const millis = date.getMilliseconds().toString().padStart(3, '0');

    return `${minutes}:${seconds}.${millis}`;
};

export const getGapToLeader = (drivers: RaceDriver[], driver: RaceDriver): string => {
    if (driver.result.dnf) {
        return `LAP ${driver.result.dnf_lap}`;
    }

    if (driver.result.position === 1 || raceStateStore.currentLap === 0) {
        return '-';
    }

    const leader = drivers.find(d => d.result.position === 1);

    if (! leader) {
        return '-';
    }

    return buildDeltaString(leader, driver);
};

export const getInterval = (drivers: RaceDriver[], driver: RaceDriver): string => {
    if (driver.result.dnf || driver.result.position === 1 || raceStateStore.currentLap === 0) {
        return '-';
    }

    const driverAhead = drivers.find(d => d.result.position === driver.result.position - 1);

    if (! driverAhead) {
        return '-';
    }

    return buildDeltaString(driverAhead, driver);
};

export const buildDeltaString = (leadingDriver: RaceDriver, trailingDriver: RaceDriver): string => {
    const gap = Math.abs(trailingDriver.result.total - leadingDriver.result.total);

    const date = new Date(gap);

    const minutes = date.getMinutes();
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const millis = date.getMilliseconds().toString().padStart(3, '0');

    if (gap > raceStateStore.lapDetails.baseLaptime) {
        const totalTimesLapped = Math.floor(gap / raceStateStore.lapDetails.baseLaptime);
        const label = totalTimesLapped > 1 ? 'laps' : 'lap';
        return `+ ${totalTimesLapped} ${label}`;
    }

    return `+ ${minutes}:${seconds}.${millis}`;
};
