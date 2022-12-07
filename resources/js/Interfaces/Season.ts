import { Race } from '@/Interfaces/Race';
import Series from '@/Interfaces/Series';

export default interface Season {
    id: string,
    series?: Series,
    races?: Race[],
    name: string,
    full_name?: string,
    year: number,
    started: boolean,
    can_start?: boolean,
    can_complete?: boolean,
}
