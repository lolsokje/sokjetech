<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <h3>Weekend intro</h3>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import RaceWeekend from '@/Shared/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
