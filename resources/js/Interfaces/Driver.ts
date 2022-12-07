export default interface Driver {
    id: string,
    universe?: Universe,
    first_name?: string,
    last_name?: string,
    full_name?: string,
    dob?: string,
    readable_dob?: string,
    edit_dob?: string,
    country: string,
    shared?: boolean,
}
