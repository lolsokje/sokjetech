import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export class Result {
    position;
    starting_position: number;
    starting_bonus: number;
    dnf: string | null;
    fastest_lap: boolean;
    fastest_lap_roll: number | null;
    stints: number[];

    constructor (driver: RaceDriver) {
        this.position = driver.result.position;
        this.starting_position = driver.result.starting_position;
        this.starting_bonus = driver.result.bonus;
        this.dnf = driver.result.dnf;
        this.fastest_lap = driver.result.fastest_lap;
        this.fastest_lap_roll = driver.result.fastest_lap_roll;
        this.stints = driver.result.stints;
    }
}
