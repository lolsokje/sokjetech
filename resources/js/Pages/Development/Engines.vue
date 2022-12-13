<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Engine development</h2>

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.engines.store', [season])"
    />
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import EngineDevelopment from '@/Shared/EngineDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentEngine from '@/Interfaces/DevelopmentEngine';

interface Props {
    season: SeasonInterface,
    engines: DevelopmentEngine[],
}

defineProps<Props>();
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
