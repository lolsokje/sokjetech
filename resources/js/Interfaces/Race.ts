import Circuit from '@/Interfaces/Circuit';
import Season from '@/Interfaces/Season';

export interface Race {
    id: string,
    circuit_id: string,
    circuit_variation_id: string,
    climate_id: string,
    name: string,
    order: number,
    race_type: number,
    duration: number,
    current_lap: number,
    qualifying_started: boolean,
    qualifying_completed: boolean,
    started: boolean,
    completed: boolean,
    season: Season,
    season_name: string,
    qualifying_details?: QualifyingDetails,
    race_details?: RaceDetails,
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

interface RaceDetails {
    current_stint: number,
    fastest_lap_awarded: boolean,
}
