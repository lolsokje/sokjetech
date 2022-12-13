import DevelopmentItem from '@/Interfaces/DevelopmentItem';

export default interface DevelopmentEngine extends DevelopmentItem {
    base_engine_id: string,
    name: string,
    rebadge: boolean,
    individual_rating: boolean,
}
