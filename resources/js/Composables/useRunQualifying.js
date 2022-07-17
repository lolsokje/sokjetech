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

export function fillDriverRuns (drivers, currentSession, results) {
    if (results.length) {
        results.forEach(result => {
            const driver = drivers.find(d => d.id === result.driver_id);
            driver.runs = result.runs;
            driver.position = result.position;
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
