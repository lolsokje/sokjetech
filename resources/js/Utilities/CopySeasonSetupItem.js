export default class CopySeasonSetupItem {
    constructor (label, entity, dependency = null, checked = false, copying = false, completed = false) {
        this.label = label;
        this.entity = entity;
        this.dependency = dependency;
        this.checked = checked;
        this.copying = copying;
        this.completed = completed;
    }
}
