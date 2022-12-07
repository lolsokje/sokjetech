import RaceResult from '@/Interfaces/RaceResult';

export default interface Racer {
    id: string,
    full_name: string,
    team_name: string,
    number: number,
    points: number,
    background_colour: string,
    style_string: string,
    results?: RaceResult[],
}
