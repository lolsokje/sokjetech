import DriverResults from '@/Interfaces/DriverResults';
import RaceResult from '@/Interfaces/RaceResult';
import TeamResults from '@/Interfaces/TeamResults';
import TeamRaceResult from '@/Interfaces/TeamRaceResult';

const getDriverPoints = (drivers: DriverResults[]): void => {
    drivers.forEach(driver => {
        driver.points = Object
            .values(driver.results)
            .reduce((points: number, currentResult: RaceResult) => points + currentResult.points, 0);
    });
};

const getTeamPoints = (teams: TeamResults[]) => {
    teams.forEach(team => {
        let points = 0;
        Object.values(team.results).forEach((drivers: TeamRaceResult) => {
            Object.values(drivers.results).forEach((result: RaceResult) => points += result.points);
        });
        team.points = points;
    });
};

const sortResults = (entities: DriverResults[] | TeamResults[]): void => {
    entities.sort((entityOne, entityTwo) => {
        if (entityOne.points === entityTwo.points) {
            return calculateTieBreaker(entityOne, entityTwo);
        }
        return entityTwo.points - entityOne.points;
    });
};

const calculateTieBreaker = (entityOne, entityTwo): number => {
    // will probably break with team tiebreakers
    const resultsTallyOne = getResultsTallyPerPosition(entityOne);
    const resultsTallyTwo = getResultsTallyPerPosition(entityTwo);
    let result = 0;

    for (let [ position, tally ] of Object.entries(resultsTallyOne)) {
        const entityTwoLowestPosition = getLowestFinishingPosition(resultsTallyTwo);

        // if driver two's best finishing position is better than driver one's, sort driver two ahead and break the loop
        if (entityTwoLowestPosition < position) {
            result = 1;
            break;
        }

        const entityTwoTally = resultsTallyTwo[position];

        // if the second driver hasn't finished in this position, sort the first driver ahead and break the loop
        if (entityTwoTally === undefined) {
            result = -1;
            break;
        }

        // if the second driver has finished in this position more than the first driver, sort the second driver ahead and break the loop
        if (entityTwoTally > tally) {
            result = 1;
            break;
        }

        // if the second driver has finished in this position less than the first driver, sort the first driver ahead and break the loop
        if (entityTwoTally < tally) {
            result = -1;
            break;
        }

        // else don't sort and continue to the next position
    }

    return result;
};

const getLowestFinishingPosition = (tallies: object[]): number => {
    const positionArray = Object.keys(tallies).map(position => parseInt(position));

    return Math.min(...positionArray);
};

const getResultsTallyPerPosition = (entity): object[] => {
    const results = [];

    Object.values(entity.results).forEach(result => {
        if (result.dnf) {
            // hacky way of getting DNFs to sort at the bottom
            if (!results[999999]) {
                results[999999] = 1;
            } else {
                results[999999]++;
            }
        } else {
            const position = result.position;

            if (!results[position]) {
                results[position] = 1;
            } else {
                results[result.position]++;
            }
        }
    });

    return results;
};

const getTopPerformers = (standings: DriverResults[] | TeamResults[], amountToShow: number): void => {
    standings.splice(amountToShow);
};

export { getDriverPoints, getTeamPoints, sortResults, getTopPerformers };
