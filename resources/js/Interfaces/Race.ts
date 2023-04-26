import Circuit from '@/Interfaces/Circuit';

export interface Race {
    id: string,
    name: string,
    order: number,
    qualifying_started: boolean,
    qualifying_completed: boolean,
    started: boolean,
    completed: boolean,
    season: string,
    season_name: string,
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
