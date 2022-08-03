export class TabLink {
    constructor (path, label, params = [], show = true) {
        this.path = path;
        this.label = label;
        this.params = params;
        this.show = show;

        this.children = [];
    }

    addChild = (child) => {
        this.children.push(child);
    };

    addChildren = (...children) => {
        children.forEach(child => this.addChild(child));
    };

    hasChildren = () => {
        return this.children.length > 0;
    };

    getChildren = () => {
        return this.children;
    };

    getRoute = () => {
        if (this.path) {
            return route(this.path, this.params);
        }
    };

    isActive = () => {
        return route().current(this.path, this.params);
    };
}
