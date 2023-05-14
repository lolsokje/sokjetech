export default interface RaceResult {
    dnf: string | null,
    fastest_lap: boolean,
    points: number,
    position: string | number,
    starting_position: number,
    position_change: number,
    total: number,
    bonus: number,
    stints: number[],
    fastest_lap_roll: number | null,
}
