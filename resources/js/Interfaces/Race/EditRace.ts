import RaceDuration from '@/Interfaces/Race/RaceDuration';

export default interface EditRace {
    id: string,
    circuit_id: string,
    circuit_variation_id: string,
    climate_id: string,
    name: string,
    duration: RaceDuration,
    race_type: number,
    distance_type: string,
}
