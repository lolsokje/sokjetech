import { Race } from '@/Interfaces/Race';
import Series from '@/Interfaces/Series';
import Entrant from '@/Interfaces/Entrant';

export default interface Season {
    id: string,
    series?: Series,
    races?: Race[],
    entrants?: Entrant[],
    name: string,
    full_name?: string,
    year: number,
    started: boolean,
    can_start?: boolean,
    can_complete?: boolean,
    last_point_paying_position?: number,
    has_active_race?: boolean,
}
