export default interface CircuitVariation {
    id: string,
    name: string,
    length: {
        default: number,
        km: number,
        m: number,
    }
    laptime: {
        base: number,
        readable: string,
    },
    multipliers: {
        team: number,
        engine: number,
    }
}
