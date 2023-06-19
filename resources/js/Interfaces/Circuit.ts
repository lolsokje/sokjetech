import Climate from '@/Interfaces/Climate';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';

export default interface Circuit {
    id: string,
    name: string,
    country: string,
    user_id: string,
    default_climate_id: string,
    shared: boolean,
    races?: Array<object>,
    default_climate: Climate,
    races_count?: number,
    variations: CircuitVariation[],
}
