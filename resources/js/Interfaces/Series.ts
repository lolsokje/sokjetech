import Universe from '@/Interfaces/Universe';
import Season from '@/Interfaces/Season';
import { Engine } from '@/Interfaces/Engine';

export default interface Series {
    id: string,
    name: string,
    universe?: Universe,
    seasons?: Array<Season>,
    engines?: Array<Engine>
}
