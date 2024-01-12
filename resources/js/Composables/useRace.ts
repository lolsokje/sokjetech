import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export const getPositionChange = (startingPosition: number, position: number): number => {
    return startingPosition - position;
};

export const getPositionChangeIconClasses = (positionChange: number): string => {
    if (positionChange === 0) {
        return 'positions-unchanged';
    }

    if (positionChange > 0) {
        return 'positions-gained';
    }

    return 'positions-lost';
};

export const getPositionChangeIcon = (positionChange: number): string => {
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
