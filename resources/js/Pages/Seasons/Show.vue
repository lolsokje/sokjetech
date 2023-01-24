<template>
    <h1>{{ season.name }}</h1>

    <BackLink :backTo="route('series.seasons.index', [season.series])" label="season overview"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';

defineProps({
    season: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
