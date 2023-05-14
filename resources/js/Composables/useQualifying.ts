import QualifyingDriver from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export const sortDriversByPosition = (drivers: QualifyingDriver[], currentSession: number): void => {
    drivers.sort((a, b) => a.result.sessions[currentSession].position - b.result.sessions[currentSession].position);
};

export const sortDriversByTotal = (drivers: QualifyingDriver[]): void => {
    drivers.sort((a, b) => b.ratings.total_rating - a.ratings.total_rating);
};

export const sortDriversBySessionTotal = (drivers: QualifyingDriver[]): void => {
    drivers.sort((a, b) => b.result.total - a.result.total);
};

export const setDriverPositions = (drivers: QualifyingDriver[], currentSession: number): void => {
    drivers.forEach((driver, index: number) => {
        driver.result.sessions[currentSession].position = index + 1;
    });
};
