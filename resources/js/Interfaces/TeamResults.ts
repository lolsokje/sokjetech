import TeamRaceResult from '@/Interfaces/TeamRaceResult';

export default interface TeamResults {
    id: string,
    full_name: string,
    team_name: string,
    team_principal: string,
    driver_count: number,
    background_colour: string,
    style_string: string,
    points: number,
    results: TeamRaceResult[],
}
