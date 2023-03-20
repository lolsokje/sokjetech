import { router } from '@inertiajs/vue3';

export const sortTable = (params, field = null) => {
    if (field) {
        params.field = field;
    }
    params.direction = params.direction === '' || params.direction === 'asc' ? 'desc' : 'asc';
};

export const filter = (params, route) => {
    const requestParams = getRequestParams(params);
    router.get(route, requestParams, { replace: true, preserveState: true });
};

export const getRequestParams = (params) => {
    let requestParams = { ...params };

    Object.entries(requestParams).forEach((entry) => {
        const [ key, value ] = entry;

        if (value === null || value === '' || value === false) {
            delete requestParams[key];
        }
    });

    return requestParams;
};
