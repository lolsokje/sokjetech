interface RaceResultDriver {
    first_name: string,
    last_name: string,
    number: number,
}

interface RaceResultRatings {
    starting_bonus: number,
    driver_rating: number,
    team_rating: number,
    engine_rating: number,
    total: number,
    starting_total: number,
    driver_reliability: number,
    team_reliability: number,
    engine_reliability: number,
}

interface RaceResultPerformance {
    starting_position: number,
    position: number,
    position_change: number,
    stints: number[],
    stints_total: number,
    race_total: number,
    fastest_lap_roll: number | null,
    dnf: string | null,
    fastest_lap: boolean,
    points: number,
}

interface RaceResultTeam {
    name: string,
    style_string: string,
    accent_colour: string,
}

export default interface RaceResult {
    id: string,
    driver: RaceResultDriver
    ratings: RaceResultRatings
    performance: RaceResultPerformance
    team: RaceResultTeam,
}
