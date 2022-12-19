import RaceResult from '@/Interfaces/RaceResult';

export default interface TeamRaceResult {
    [key: string]: {
        number: number,
        results: RaceResult[],
    };
}
