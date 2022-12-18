export default interface PointSystem {
    id: string,
    fastest_lap_point_awarded: boolean,
    pole_position_point_awarded: boolean,
    fastest_lap_point_amount: number,
    pole_position_point_amount: number,
    fastest_lap_determination: string | null,
    fastest_lap_min_rng: number,
    fastest_lap_max_rng: number,
}
