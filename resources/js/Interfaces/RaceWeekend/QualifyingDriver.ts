interface Team {
    team_name: string,
    short_team_name: string,
    style_string: string,
    primary_colour: string,
    secondary_colour: string,
    accent_colour: string,
}

interface QualifyingRatings {
    driver_rating: number,
    team_rating: number,
    engine_rating: number,
    driver_reliability: number,
    team_reliability: number,
    engine_reliability: number,
    total_rating: number,
}

interface QualifyingResult {
    id: number,
    position: number,
    sessions: {
        [key: number]: {
            position?: number,
            runs?: number[],
        },
    },
    best_stint?: number | null,
    total?: number,
}

export default interface QualifyingDriver {
    id: string,
    entrant_id: string,
    full_name: string,
    number: number,
    team: Team,
    ratings: QualifyingRatings,
    result: QualifyingResult,
}