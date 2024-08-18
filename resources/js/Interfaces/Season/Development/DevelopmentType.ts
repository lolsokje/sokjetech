export type DevelopmentType = {
    name: string,
    components: string[],
};

export default class Type implements DevelopmentType {
    name;
    components;

    constructor (name: string, components: string[]) {
        this.name = name;
        this.components = components;
    }
}
