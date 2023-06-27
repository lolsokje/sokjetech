export default interface CalendarSeason {
    id: string,
    full_name: string,
    series: {
        id: string,
        name: string,
    },
    started: boolean,
    completed: boolean,
    can_start: boolean,
    can_complete: boolean,
}
