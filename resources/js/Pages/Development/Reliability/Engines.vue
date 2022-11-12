<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Engine reliability</h2>

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.reliability.engines.store', [season])"
                       type="reliability"/>
</template>

<script setup>
import EngineDevelopment from '@/Shared/EngineDevelopment.vue';

const props = defineProps({
    season: Object,
    engines: Array,
});
</script>

<script>
import Season from '@/Layouts/Season.vue';
import BackLink from '@/Shared/BackLink.vue';

export default {
    components: { BackLink },
    layout: Season,
};
</script>
