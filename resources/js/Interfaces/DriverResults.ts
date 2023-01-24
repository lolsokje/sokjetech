import RaceResult from '@/Interfaces/RaceResult';

export default interface DriverResults {
    id: string,
    full_name: string,
    team_name: string,
    number: number,
    background_colour: string,
    style_string: string,
    points: number,
    results: RaceResult[],
}
