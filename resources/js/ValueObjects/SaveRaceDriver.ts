import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import { Ratings } from '@/ValueObjects/Ratings';
import { Result } from '@/ValueObjects/Result';

export class SaveRaceDriver {
    id: string;
    ratings: Ratings;
    result: Result;

    constructor (driver: RaceDriver) {
        this.id = driver.id;
        this.ratings = new Ratings(driver);
        this.result = new Result(driver);
    }
}
