import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

export default interface FastestLapDetails {
    laptime: number,
    driver: RaceDriver | null,
    lap: number,
}
