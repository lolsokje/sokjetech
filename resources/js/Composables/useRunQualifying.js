const ASCENDING = 'asc';
const DESCENDING = 'desc';

export function getRoll (min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

export function getBestRun (runs) {
    return runs ? Math.max(...runs) : null;
}

export function getSessionTotal (driver) {
    return driver.result.best_stint + driver.ratings.total_rating;
}

export function sortDriversByTotalRating (drivers) {
    return sortDrivers(drivers, 'ratings.total_rating');
}

export function sortDriversByPosition (drivers) {
    return sortDrivers(drivers, 'result.position', ASCENDING);
}

export function sortDriversByTotal (drivers) {
    return sortDrivers(drivers, 'result.total');
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

export function calculateDriverTotals (drivers, currentSession) {
    drivers.forEach(driver => {
        driver.result.best_stint = getBestRun(driver.result.runs[currentSession]);
        driver.result.total = getSessionTotal(driver);
    });

    if (drivers[0].result.runs.length) {
        sortDriversByPosition(drivers);
    } else {
        sortDriversByTotalRating(drivers);
    }
}

export function performQualifyingRun (store, participationCheck = null) {
    const currentSessionIndex = store.getCurrentSessionIndex();
    const currentSessionRunCount = store.getCurrentSessionRunCount();
    store.getDrivers().forEach((driver, index) => {
        if (driver.result.runs[currentSessionIndex] === undefined) {
            driver.result.runs[currentSessionIndex] = [];
        }

        if (driver.result.runs[currentSessionIndex][currentSessionRunCount] === undefined) {
            driver.result.runs[currentSessionIndex][currentSessionRunCount] = [];
        }

        if (participationCheck === null || participationCheck(index)) {
            driver.result.runs[currentSessionIndex][currentSessionRunCount] = getRoll(store.getMinRng(), store.getMaxRng());
            calculateSessionBestAndTotal(driver, store.getCurrentSessionIndex());
        }
    });

    sortDriversByTotal(store.getDrivers());
}

export function calculateSessionBestAndTotal (driver, currentSession) {
    if (!driver.result.runs[currentSession].length) {
        driver.result.best_stint = null;
        driver.result.total = null;
    } else {
        driver.result.best_stint = getBestRun(driver.result.runs[currentSession]);
        driver.result.total = getSessionTotal(driver);
    }
}

function sortDrivers (drivers, key, direction = DESCENDING) {
    const path = key.split('.');
    return drivers.sort((driverOne, driverTwo) => {
        let driverOneSort = driverOne;
        let driverTwoSort = driverTwo;

        for (key of path) {
            driverOneSort = driverOneSort[key];
            driverTwoSort = driverTwoSort[key];
        }

        if (direction === ASCENDING) {
            return driverOneSort - driverTwoSort;
        } else {
            return driverTwoSort - driverOneSort;
        }
    });
}
