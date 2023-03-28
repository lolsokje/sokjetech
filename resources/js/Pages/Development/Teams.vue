<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Team development"
    />

    <TeamDevelopment :season="season" :teams="teams" :form-route="route('seasons.development.teams.store', [season])"/>
</template>

<script setup lang="ts">
import TeamDevelopment from '@/Shared/TeamDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentTeam from '@/Interfaces/DevelopmentTeam';
import Breadcrumb from '@/Components/Breadcrumb.vue';

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
