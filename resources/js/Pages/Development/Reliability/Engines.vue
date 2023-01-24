<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Engine reliability</h2>

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.reliability.engines.store', [season])"
                       type="reliability"
    />
</template>

<script setup lang="ts">
import EngineDevelopment from '@/Shared/EngineDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentEngine from '@/Interfaces/DevelopmentEngine';

interface Props {
    season: SeasonInterface,
    engines: DevelopmentEngine[],
}

const props = defineProps<Props>();
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';
import BackLink from '@/Shared/BackLink.vue';

export default {
    components: { BackLink },
    layout: Season,
};
</script>
