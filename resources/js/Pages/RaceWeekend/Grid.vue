<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <h3>{{ race.name }} Starting Grid</h3>

    <table class="table table-bordered table-dark table-narrow">
        <thead>
        <tr>
            <th class="text-center">Pos</th>
            <th class="colour-accent"></th>
            <th>Driver</th>
            <th class="text-center">#</th>
            <th>Team</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="small-centered">{{ driver.position }}</td>
            <td class="colour-accent" :style="`background-color: ${driver.background_colour}`"></td>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td :style="driver.style_string" class="small-centered">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
});

onMounted(() => sortDriversByPosition(props.drivers));
</script>

<script>
import RaceWeekend from '@/Shared/Layouts/RaceWeekend';

export default {layout: RaceWeekend};
</script>
