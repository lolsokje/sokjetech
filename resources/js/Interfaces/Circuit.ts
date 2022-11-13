export default interface Circuit {
    id: string,
    name: string,
    country: string,
    user_id: string,
    shared: boolean,
    races?: Array<object>,
    races_count?: number,
}
