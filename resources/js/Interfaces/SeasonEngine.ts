import { Engine } from '@/Interfaces/Engine';

export default interface SeasonEngine {
    id: string,
    name: string,
    rating: number,
    reliability: number,
    rebadge: boolean,
    individual_rating: boolean,
    base_engine: Engine,
    base_engine_id: string,
}
