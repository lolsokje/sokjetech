<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <h3>{{ race.name }} Starting Grid</h3>

    <table class="table table-dark table-narrow">
        <thead>
        <tr>
            <th class="text-center">Pos</th>
            <th style="max-width: 5px"></th>
            <th>Driver</th>
            <th class="text-center">#</th>
            <th>Team</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="text-center">{{ driver.position }}</td>
            <td style="max-width: 5px" :style="`background-color: ${driver.background_colour}`"></td>
            <td>{{ driver.full_name }}</td>
            <td :style="driver.style_string" class="text-center">{{ driver.number }}</td>
            <td>{{ driver.team_name }}</td>
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

export default { layout: RaceWeekend };
</script>
