export default class CopySeasonSetupItem {
    constructor (label, entity, ...dependencies) {
        this.label = label;
        this.entity = entity;
        this.dependencies = dependencies;
        this.checked = false;
        this.copying = false;
        this.completed = false;
        this.fail = false;
        this.error = null;
    }
}
