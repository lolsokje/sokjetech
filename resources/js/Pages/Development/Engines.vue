<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Engine development</h2>

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.engines.store', [season])"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import EngineDevelopment from '@/Shared/EngineDevelopment';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    engines: {
        type: Array,
        required: true,
    },
});

// const state = reactive({
//     error: null,
//     completed: false,
//     hideInputs: false,
//     min: 0,
//     max: 0,
//     editRatings: false,
// });
//
// const form = useForm({
//     engines: [],
// });
//
// function applyDevRanges () {
//     state.error = null;
//     if (Development.validateDevRange(state)) {
//         Development.applyDevRangesToItems(form.engines, state);
//     } else {
//         state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
//     }
// }
//
// function runDev () {
//     state.error = null;
//     if (Development.performDev(form.engines)) {
//         updateEnginesUsingParentRating();
//         state.completed = true;
//     } else {
//         state.error = 'One of the engines\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
//     }
// }
//
// function updateEnginesUsingParentRating () {
//     form.engines.filter(e => e.individual_rating === false).forEach(engine => {
//         const parentEngine = form.engines.find(e => e.base_engine_id === engine.base_engine_id && e.rebadge === false);
//         if (!parentEngine) {
//             return;
//         }
//
//         engine.dev = parentEngine.dev;
//         engine.new = parentEngine.new;
//     });
// }
//
// function store () {
//     Development.storeDev(form, route('seasons.development.engines.store', [ props.season ]), state);
// }
//
// const devCompleted = computed(() => !state.completed);
// const inputsHidden = computed(() => !state.hideInputs);
//
// onMounted(() => {
//     props.engines.forEach((engine) => {
//         form.engines.push(new DevelopmentEngine(engine));
//     });
//
//     form.engines.sort((a, b) => a.name.localeCompare(b.name));
// });
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
