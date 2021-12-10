class MenuItem {
    url;
    label;
    icon;
    authRequired = false;

    constructor (url, label, icon, authRequired = false) {
        this.url = url;
        this.label = label;
        this.icon = icon;
        this.authRequired = authRequired;
    }
}

export default MenuItem;
