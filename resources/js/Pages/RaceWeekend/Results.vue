<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="races overview"/>

    <h3>Results</h3>

    <p>
        <span class="fst-italic">italic = pole position</span>
        <template v-if="race.season.point_system.fastest_lap_point_awarded">
            , <span class="text-decoration-underline">underlined = fastest lap</span>
        </template>
    </p>
    <table class="table">
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
            <BackgroundColourCell :backgroundColour="driver.background_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td class="text-center text-uppercase" :class="getResultDisplayClasses(driver)">
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
import BackgroundColourCell from '@/Components/BackgroundColourCell';

const props = defineProps({
    race: Object,
    drivers: Array,
});

const getResultDisplayClasses = (driver) => {
    const classes = [];

    if (driver.dnf) {
        classes.push('bg-danger');
    }

    if (driver.starting_position === 1) {
        classes.push('fst-italic');
    }

    if (driver.fastest_lap) {
        classes.push('text-decoration-underline');
    }

    return classes.join(' ');
};

onMounted(() => sortDriversByPosition(props.drivers));
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
