export default class CopySeasonSetupItem {
    constructor (label, entity, dependency = null) {
        this.label = label;
        this.entity = entity;
        this.dependency = dependency;
        this.checked = false;
        this.copying = false;
        this.completed = false;
        this.fail = false;
        this.error = null;
    }
}
