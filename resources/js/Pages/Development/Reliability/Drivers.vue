<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h2>Driver reliability</h2>

    <DriverDevelopment :season="season" :drivers="drivers"
                       :form-route="route('seasons.development.reliability.drivers.store', [season])"
                       type="reliability"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import DriverDevelopment from '@/Shared/DriverDevelopment.vue';

defineProps({
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
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
