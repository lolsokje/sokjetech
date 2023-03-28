<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Results"
    />

    <div id="screenshot-target">
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
                <td class="smallest-centered">{{ driver.result.position }}</td>
                <td class="colour-accent"></td>
                <BackgroundColourCell :backgroundColour="driver.team.background_colour"/>
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="smallest-centered" :style="driver.team.style_string">{{ driver.number }}</td>
                <td class="padded-left">{{ driver.team.team_name }}</td>
                <td class="text-center text-uppercase" :class="getResultDisplayClasses(driver)">
                    {{ driver.result.dnf ? driver.result.dnf : driver.result.points }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup>
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    race: Object,
    drivers: Array,
});

const getResultDisplayClasses = (driver) => {
    const classes = [];

    if (driver.result.dnf) {
        classes.push('position-dnf');
    }

    if (driver.result.starting_position === 1) {
        classes.push('fst-italic');
    }

    if (driver.result.fastest_lap) {
        classes.push('text-decoration-underline');
    }

    return classes.join(' ');
};

onMounted(() => sortDriversByPosition(props.drivers));
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
