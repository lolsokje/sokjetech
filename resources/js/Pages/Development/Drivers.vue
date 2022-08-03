<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Driver development</h2>

    <DriverDevelopment :form-route="route('seasons.development.drivers.store', [season])" :drivers="drivers"
                       :season="season"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import DriverDevelopment from '@/Shared/DriverDevelopment';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
