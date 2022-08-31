export default class DevelopmentDriver {
    constructor (driver, isReliability = false) {
        this.id = driver.id;
        this.number = driver.number;
        this.team_name = driver.entrant.full_name;
        this.team_style = driver.entrant.style_string;
        this.accent_colour = driver.entrant.accent_colour;
        this.full_name = driver.driver.full_name;
        this.rating = isReliability ? driver.reliability : driver.rating;
        this.min = 0;
        this.max = 0;
        this.dev = 0;
        this.new = isReliability ? driver.reliability : driver.rating;
    }
}
