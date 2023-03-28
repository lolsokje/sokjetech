<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Engine reliability"
    />

    <EngineDevelopment :season="season" :engines="engines"
                       :form-route="route('seasons.development.reliability.engines.store', [season])"
                       type="reliability"
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
