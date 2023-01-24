import Racer from '@/Interfaces/Racer';
import SeasonEngine from '@/Interfaces/SeasonEngine';

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
    engine: SeasonEngine,
    active_racers: Racer[],
    driver_count?: number,
    rating: number,
    points: number,
    style_string: string,
}
