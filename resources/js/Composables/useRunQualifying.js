const ASCENDING = 'asc';
const DESCENDING = 'desc';

export function getRoll (min, max) {
    return Math.floor(Math.random() * (max - min) + min);
}

export function getBestRun (runs) {
    return Math.max(...runs);
}

export function getSessionTotal (driver) {
    return driver.best_stint + driver.total_rating;
}

export function sortDriversByTotalRating (drivers) {
    return sortDrivers(drivers, 'total_rating');
}

export function sortDriversByPosition (drivers) {
    return sortDrivers(drivers, 'position', ASCENDING);
}

export function sortDriversByTotal (drivers) {
    return sortDrivers(drivers, 'total');
}

/**
 * Sorts by total if both compare drivers have completed the same amount of stints, or by stint count if
 * either driver has DNFd.
 *
 * This is done to prevent retired drivers with high early stints from being sorted ahead of remaining drivers
 * with lower stint rolls
 *
 * @param {array} drivers
 */
export function sortDriversByTotalAndStintCount (drivers) {
    drivers.sort((driverOne, driverTwo) => {
        if (driverOne.stints.length === driverTwo.stints.length) {
            return driverTwo.total - driverOne.total;
        }
        return driverTwo.stints.length - driverOne.stints.length;
    });
}

export function fillDriverRuns (drivers, currentSession, results) {
    if (results.length) {
        results.forEach(result => {
            const driver = drivers.find(d => d.id === result.driver_id);
            driver.runs = result.runs;
            driver.position = result.position;
            driver.driver_rating = result.driver_rating;
            driver.team_rating = result.team_rating;
            driver.engine_rating = result.engine_rating;
            driver.total_rating = driver.driver_rating + driver.team_rating + driver.engine_rating;
            driver.best_stint = getBestRun(driver.runs[currentSession]);
            driver.total = getSessionTotal(driver);
        });

        sortDriversByPosition(drivers);
    } else {
        drivers.forEach(driver => {
            driver.runs = [];
            driver.runs[currentSession] = [];
        });

        sortDriversByTotalRating(drivers);
    }
}

export function performQualifyingRun (store, participationCheck = null) {
    store.getDrivers().forEach((driver, index) => {
        if (participationCheck === null || participationCheck(index)) {
            driver.runs[store.getCurrentSessionIndex()][store.getCurrentSessionRunCount()] = getRoll(store.getMinRng(), store.getMaxRng());
            calculateSessionBestAndTotal(driver, store.getCurrentSessionIndex());
        }
    });

    sortDriversByTotal(store.getDrivers());
}

export function calculateSessionBestAndTotal (driver, currentSession) {
    if (!driver.runs[currentSession].length) {
        driver.best_stint = null;
        driver.total = null;
    } else {
        driver.best_stint = getBestRun(driver.runs[currentSession]);
        driver.total = getSessionTotal(driver);
    }
}

function sortDrivers (drivers, key, direction = DESCENDING) {
    return drivers.sort((driverOne, driverTwo) => {
        if (direction === ASCENDING) {
            return driverOne[key] - driverTwo[key];
        } else {
            return driverTwo[key] - driverOne[key];
        }
    });
}
