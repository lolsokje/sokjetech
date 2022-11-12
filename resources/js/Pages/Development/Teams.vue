<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Team development</h2>

    <TeamDevelopment :season="season" :teams="teams" :form-route="route('seasons.development.teams.store', [season])"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import TeamDevelopment from '@/Shared/TeamDevelopment.vue';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    teams: {
        type: Array,
        required: true,
    },
});
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
