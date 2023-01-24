export const addStint = (stints) => {
    const lastOrder = getLastStintOrder(stints);
    stints.push({
        order: lastOrder + 1,
        min_rng: 0,
        max_rng: 30,
        reliability: false,
        use_team_rating: false,
        use_driver_rating: false,
        use_engine_rating: false,
    });
};

export const copyStint = (order, stints) => {
    const lastStintOrder = getLastStintOrder(stints);
    const sourceStint = stints.find(s => s.order === order);

    const fields = [
        'min_rng',
        'max_rng',
        'reliability',
        'use_driver_rating',
        'use_team_rating',
        'use_engine_rating',
    ];

    const newStint = {};

    fields.forEach(field => newStint[field] = sourceStint[field]);

    newStint.order = lastStintOrder + 1;

    stints.push(newStint);
};

export const getLastStintOrder = (stints) => {
    return stints.at(-1).order;
};
