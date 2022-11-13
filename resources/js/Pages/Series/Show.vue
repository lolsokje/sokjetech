<template>
    <BackLink :backTo="route('universes.series.index', [universe])" label="series overview"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';

defineProps({
    universe: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
