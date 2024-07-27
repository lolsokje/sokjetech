import { RaceEventType } from '@/Interfaces/RaceWeekend/RaceEventType';

export default interface RaceEvent {
    type: RaceEventType,
    lap: number,
    driver_id: string,
    description?: string,
}
