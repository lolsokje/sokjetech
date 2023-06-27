import Circuit from '@/Interfaces/Circuit';
import { Participant } from '@/Interfaces/Race';

export default interface CalendarRace {
    id: string,
    order: number,
    circuit: Circuit,
    name: string,
    started: boolean,
    completed: boolean,
    qualifying_started: boolean,
    qualifying_completed: boolean,
    duration: string,
    postfix: string,
    pole: Participant | null,
    winner: Participant | null,
}
