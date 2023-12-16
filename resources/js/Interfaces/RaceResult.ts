export default interface RaceResult {
    dnf: string | null,
    dnf_lap: number,
    fastest_lap: boolean,
    points: number,
    position: string | number,
    starting_position: number,
    position_change: number,
    last: number,
    total: number,
    bonus: number,
    stints: number[],
    fastest_lap_roll: number | null,
}
