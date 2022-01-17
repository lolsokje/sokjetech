export default class DevelopmentTeam {
    constructor (team, isReliability = false) {
        this.id = team.id;
        this.name = team.team.full_name;
        this.style = team.style_string;
        this.rating = isReliability ? team.reliability : team.rating;
        this.min = 0;
        this.max = 0;
        this.dev = 0;
        this.new = isReliability ? team.reliability : team.rating;
    }
}
