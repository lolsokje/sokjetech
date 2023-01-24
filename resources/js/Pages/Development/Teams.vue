<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Team development</h2>

    <TeamDevelopment :season="season" :teams="teams" :form-route="route('seasons.development.teams.store', [season])"/>
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import TeamDevelopment from '@/Shared/TeamDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentTeam from '@/Interfaces/DevelopmentTeam';

interface Props {
    season: SeasonInterface,
    teams: DevelopmentTeam[],
}

defineProps<Props>();
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
