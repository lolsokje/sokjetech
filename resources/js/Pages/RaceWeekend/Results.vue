<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="races overview"/>

    <h3>Results</h3>

    <table class="table table-dark table-bordered">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="colour-accent"></th>
            <th class="colour-accent"></th>
            <th class="padded-left">DRIVER</th>
            <th></th>
            <th class="padded-left">TEAM</th>
            <th class="text-center">RESULT</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="small-centered">{{ driver.position }}</td>
            <td class="colour-accent"></td>
            <td class="colour-accent" :style="`background-color: ${driver.background_colour}`"></td>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td class="text-center text-uppercase" :class="getResultDisplayClasses(driver.dnf)">
                {{ driver.dnf ? driver.dnf : driver.points }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';

const props = defineProps({
    race: Object,
    drivers: Array,
});

const getResultDisplayClasses = (dnf) => {
    return dnf ? 'bg-danger' : '';
};

onMounted(() => sortDriversByPosition(props.drivers));
</script>

<script>
import RaceWeekend from '@/Shared/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
