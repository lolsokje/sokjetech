<template>
    <Breadcrumb :link="route('seasons.races.index', race.season.id)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Results"
    />

    <div id="screenshot-target">
        <p>
            <span class="fst-italic">italic = pole position</span>
        </p>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">POS</th>
                <th></th>
                <th class="colour-accent"></th>
                <th class="padded-left">DRIVER</th>
                <th></th>
                <th class="padded-left">TEAM</th>
                <th v-if="race.fastest_lap_point_awarded"></th>
                <th class="text-center">RESULT</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in drivers" :key="driver.id">
                <td class="smallest-centered">{{ driver.result.position }}</td>
                <td class="smallest-centered" :class="getPositionChangeIconClasses(driver)">
                    <fa :icon="getPositionChangeIcon(driver)"/>
                    {{ Math.abs(driver.result.position_change) }}
                </td>
                <BackgroundColourCell :backgroundColour="driver.team.accent_colour"/>
                <td class="padded-left">{{ driver.full_name }}</td>
                <td v-if="race.fastest_lap_point_awarded" class="smallest-centered fastest-lap">
                    <fa icon="stopwatch" size="xl" v-if="driver.result.fastest_lap"/>
                </td>
                <DriverNumberCell :number="driver.number" :styleString="driver.team.style_string"/>
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

<script setup lang="ts">
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying.js';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';
import { getPositionChangeIcon, getPositionChangeIconClasses } from '@/Composables/useRace';

interface RaceInterface {
    id: string,
    name: string,
    season: {
        id: string,
        full_name: string,
    },
    fastest_lap_point_awarded: boolean,
}

interface Props {
    race: RaceInterface,
    drivers: RaceDriver[],
}

const props = defineProps<Props>();

const getResultDisplayClasses = (driver: RaceDriver): string => {
    const classes = [];

    if (driver.result.dnf) {
        classes.push('driver-dnf');
    }

    if (driver.result.starting_position === 1) {
        classes.push('fst-italic');
    }

    return classes.join(' ');
};

onMounted(() => {
    props.drivers.forEach(driver => driver.result.position_change = driver.result.starting_position - driver.result.position);
    sortDriversByPosition(props.drivers);
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
