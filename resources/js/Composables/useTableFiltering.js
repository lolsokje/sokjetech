import { Inertia } from '@inertiajs/inertia';

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
