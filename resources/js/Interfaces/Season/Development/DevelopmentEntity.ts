export type DevelopmentEntity = {
    id: string,
    label: string,
    current: number,
    extra: {
        accent: string,
        number: number,
        team: string,
        age: number,
        rebadged: boolean,
        individual_rating: boolean,
        base_engine_id: string,
    },
    styleString: string,
    min: number,
    max: number,
    rng: number,
    new: number,
}
