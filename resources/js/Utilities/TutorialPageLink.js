export default class TutorialPageLink {
    constructor (page, label, children = []) {
        this.page = page;
        this.label = label;
        this.children = children;
    }

    getLink () {
        return route('tutorials', this.page);
    }

    hasChildren () {
        return this.children.length > 0;
    }
}
