const getDriverPoints = (drivers) => {
    drivers.forEach(driver => {
        let points = 0;
        Object.values(driver.results).forEach(result => points += result.points);
        driver.points = points;
    });
};

const getTeamPoints = (teams) => {
    teams.forEach(team => {
        let points = 0;
        Object.values(team.results).forEach(drivers => {
            Object.values(drivers.results).forEach(result => points += result.points);
        });
        team.points = points;
    });
};

const getTopPerformers = (standings, amountToShow) => {
    standings.splice(amountToShow);
};

export { getDriverPoints, getTeamPoints, getTopPerformers };
