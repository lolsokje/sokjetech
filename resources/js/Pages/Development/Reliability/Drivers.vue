<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Driver reliability</h2>

    <DriverDevelopment :season="season" :drivers="drivers"
                       :form-route="route('seasons.development.reliability.drivers.store', [season])"
                       type="reliability"
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
