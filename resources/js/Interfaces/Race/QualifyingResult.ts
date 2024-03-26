interface QualifyingResultDriver {
    first_name: string,
    last_name: string,
    number: number,
}

interface QualifyingResultPerformance {
    position: number,
    sessions: object,
    best_stint: number | null,
    total: number,
}

interface QualifyingResultRatings {
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

interface QualifyingResultTeam {
    name: string,
    style_string: string,
    accent_colour: string,
}

export default interface QualifyingResult {
    id: string,
    driver: QualifyingResultDriver,
    performance: QualifyingResultPerformance,
    ratings: QualifyingResultRatings,
    team: QualifyingResultTeam,
}
