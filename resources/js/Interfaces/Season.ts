import { Race } from '@/Interfaces/Race';
import Series from '@/Interfaces/Series';
import Entrant from '@/Interfaces/Entrant';
import PointSystem from '@/Interfaces/PointSystem';
import FormatDetails from '@/Interfaces/RaceWeekend/FormatDetails';

export default interface Season {
    id: string,
    series?: Series,
    races?: Race[],
    entrants?: Entrant[],
    point_system: PointSystem,
    format_type: string,
    format: FormatDetails,
    name: string,
    full_name: string,
    year: number,
    started: boolean,
    can_start?: boolean,
    can_complete?: boolean,
    last_point_paying_position?: number,
    has_active_race?: boolean,
}
