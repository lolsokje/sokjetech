import Racer from '@/Interfaces/Racer';
import { Engine } from '@/Interfaces/Engine';

export default interface Entrant {
    id: string,
    team_id: string,
    engine_id: string,
    short_name: string,
    full_name: string,
    country: string,
    primary_colour: string,
    secondary_colour: string,
    accent_colour: string,
    background_colour: string,
    team_principal: string,
    results: Racer[],
    engine: Engine,
    driver_count?: number,
    points: number,
    style_string: string,
}
