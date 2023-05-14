import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export class Ratings {
    driver_rating: number;
    team_rating: number;
    engine_rating: number;

    constructor (driver: RaceDriver) {
        this.driver_rating = driver.ratings.driver_rating;
        this.team_rating = driver.ratings.team_rating;
        this.engine_rating = driver.ratings.engine_rating;
    }
}
