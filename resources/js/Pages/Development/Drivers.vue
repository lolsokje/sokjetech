<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Driver development</h2>

    <DriverDevelopment :form-route="route('seasons.development.drivers.store', [season])" :drivers="drivers"
                       :season="season"
    />
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import DriverDevelopment from '@/Shared/DriverDevelopment.vue';
import SeasonInterface from '@/Interfaces/Season';
import DevelopmentDriver from '@/Interfaces/DevelopmentDriver';

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
