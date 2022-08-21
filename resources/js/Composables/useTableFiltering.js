import { Inertia } from '@inertiajs/inertia';

export const sort = (params, field = null) => {
    if (field) {
        params.field = field;
    }
    params.direction = params.direction === '' || params.direction === 'asc' ? 'desc' : 'asc';
};

export const filter = (params, route) => {
    let requestParams = params;

    Object.entries(requestParams).forEach((entry) => {
        const [ key, value ] = entry;

        if (value === null || value === '' || value === false) {
            delete requestParams[key];
        }
    });

    Inertia.get(route, requestParams, { replace: true, preserveState: true });
};
