export default class CopySeasonSetupItemDependency {
    constructor (name, label, checked = true, copying = false, completed = false) {
        this.name = name;
        this.label = label;
        this.checked = checked;
        this.copying = copying;
        this.completed = completed;
    }
}
