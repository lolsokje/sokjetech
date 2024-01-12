<template>
    <Breadcrumb :link="route('seasons.races.index', race.season.id)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Results"
    />

    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ race.round }} - {{ race.name }}</h2>
            <h2 class="ms-auto">
                Results
            </h2>
        </div>
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
            <tr v-for="result in results" :key="result.id">
                <td class="smallest-centered">{{ result.performance.position }}</td>
                <td class="smallest-centered" :class="getPositionChangeIconClasses(result.performance.position_change)">
                    <fa :icon="getPositionChangeIcon(result.performance.position_change)"/>
                    {{ Math.abs(result.performance.position_change) }}
                </td>
                <BackgroundColourCell :backgroundColour="result.team.accent_colour"/>
                <td class="padded-left">
                    <DriverName :firstName="result.driver.first_name" :lastName="result.driver.last_name"/>
                </td>
                <td v-if="race.fastest_lap_point_awarded" class="smallest-centered fastest-lap">
                    <fa icon="stopwatch" size="xl" v-if="result.performance.fastest_lap"/>
                </td>
                <DriverNumberCell :number="result.driver.number" :styleString="result.team.style_string"/>
                <td class="padded-left">{{ result.team.name }}</td>
                <td class="text-center text-uppercase"
                    :class="{ 'driver-dnf': result.performance.dnf, 'fst-italic': result.performance.starting_position === 1}"
                >
                    {{ result.performance.dnf ? result.performance.dnf : result.performance.points }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { getPositionChangeIcon, getPositionChangeIconClasses } from '@/Composables/useRace';
import RaceResult from '@/Interfaces/Race/RaceResult';
import DriverName from '@/Components/DriverName.vue';

interface RaceInterface {
    id: string,
    name: string,
    round: number,
    season: {
        id: string,
        full_name: string,
    },
    fastest_lap_point_awarded: boolean,
}

interface Props {
    race: RaceInterface,
    results: RaceResult[],
}

const props = defineProps<Props>();

onMounted(() => {
    props.results.sort((a, b) => a.performance.position - b.performance.position);
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
