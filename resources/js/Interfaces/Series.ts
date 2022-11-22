interface Series {
    id: string,
    name: string,
    universe?: Universe,
    seasons?: Array<Season>,
    engines?: Array<Engine>
}
