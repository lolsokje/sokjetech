export interface FastestLapConfiguration {
    awarded: boolean,
    type: string,
    min_rng: number,
    max_rng: number,
}

export interface ReliabilityConfiguration {
    season_id: string,
    min_rng: number,
    max_rng: number,
}

export interface ReliabilityReasons {
    engine: string[],
    team: string[],
    driver: string[],
}
