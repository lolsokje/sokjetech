import Racer from '@/Interfaces/Racer';

export default interface Entrant {
    id: string,
    results: Racer[],
    driver_count?: number,
    background_colour: string,
    full_name: string,
    points: number,
    style_string: string,
}
