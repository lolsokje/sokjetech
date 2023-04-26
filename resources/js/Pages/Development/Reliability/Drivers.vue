<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Driver reliability"
    />

    <DriverDevelopment :season="season" :drivers="drivers"
                       :form-route="route('seasons.development.reliability.drivers.store', [season])"
                       type="reliability"
    />
</template>

<script setup lang="ts">
import DriverDevelopment from '@/Shared/DriverDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentDriver from '@/Interfaces/DevelopmentDriver';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    season: SeasonInterface,
    drivers: DevelopmentDriver[],
}

defineProps<Props>();
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
