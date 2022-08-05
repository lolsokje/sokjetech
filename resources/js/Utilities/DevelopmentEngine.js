export default class DevelopmentEngine {
    constructor (engine, isReliability = false) {
        this.id = engine.id;
        this.name = engine.name;
        this.rating = isReliability ? engine.reliability : engine.rating;
        this.min = 0;
        this.max = 0;
        this.dev = 0;
        this.new = isReliability ? engine.reliability : engine.rating;
        this.base_engine_id = engine.base_engine_id;
        this.rebadge = engine.rebadge;
        this.individual_rating = engine.individual_rating;
    }
}
