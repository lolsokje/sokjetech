<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Engine development"
    />

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.engines.store', [season])"
    />
</template>

<script setup lang="ts">
import EngineDevelopment from '@/Shared/EngineDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentEngine from '@/Interfaces/DevelopmentEngine';
import Breadcrumb from '@/Components/Breadcrumb.vue';

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
