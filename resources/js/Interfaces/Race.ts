import Circuit from '@/Interfaces/Circuit';
import Season from '@/Interfaces/Season';

export interface Race {
    id: string,
    name: string,
    order: number,
    qualifying_started: boolean,
    qualifying_completed: boolean,
    started: boolean,
    completed: boolean,
    season: Season,
    season_name: string,
    qualifying_details?: QualifyingDetails,
    circuit: Circuit
    pole?: Participant,
    winner?: Participant
}

export interface Participant {
    race_id: string,
    full_name: string,
    team_name: string,
    number: number,
    background_colour: string,
    style_string: string,
}

interface QualifyingDetails {
    completed_runs: number[] | number,
    current_session: number,
}
