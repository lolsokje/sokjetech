import RaceResult from '@/Interfaces/RaceResult';
import Entrant from '@/Interfaces/Entrant';
import Driver from '@/Interfaces/Driver';

export default interface Racer {
    id: string,
    full_name: string,
    team_name: string,
    number: number,
    rating: number,
    points: number,
    background_colour: string,
    style_string: string,
    active: boolean,
    driver: Driver,
    entrant: Entrant,
    results?: RaceResult[],
}
