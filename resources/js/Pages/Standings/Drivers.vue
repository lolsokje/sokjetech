<template>
    <Breadcrumb :link="route('series.seasons.index', series)"
                :linkText="series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Driver standings"
    />

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th>DRIVER</th>
            <th class="text-center">#</th>
            <th>TEAM</th>
            <th class="colour-accent"></th>
            <th class="text-center">PTS</th>
            <th class="text-center" v-for="race in races" :key="race.order">
                <template v-if="race.completed">
                    <InertiaLink :href="route('weekend.results', [race])">
                        <CountryFlag :country="race.circuit.country"/>
                    </InertiaLink>
                </template>
                <template v-else>
                    <CountryFlag :country="race.circuit.country"/>
                </template>
            </th>
        </tr>
        </thead>
        <tbody>
        <template v-for="driver in standings" :key="driver.position">
            <tr v-for="(team, index) in driver.entrants" :key="index">
                <td v-if="team.first" class="small-centered" :rowspan="driver.rowspan">
                    {{ driver.position }}
                </td>
                <td v-if="team.first" class="padded-left" :rowspan="driver.rowspan">
                    {{ driver.name }}
                </td>
                <td class="smallest-centered" :style="team.style_string">{{ team.number }}</td>
                <td class="padded-left">{{ team.name }}</td>
                <BackgroundColourCell :backgroundColour="team.accent_colour"/>
                <td v-if="team.first" class="small-centered" :rowspan="driver.rowspan">
                    {{ driver.points }}
                </td>
                <td v-for="race in races"
                    :key="race.order"
                    class="smallest-centered"
                    :class="getResultDisplayClasses(team.results[race.order])"
                >
                    {{ team.results[race.order]?.position }}
                </td>
            </tr>
        </template>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { getResultClasses } from '@/Composables/useResultPage.js';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import SeasonInterface from '@/Interfaces/Season';
import { Race } from '@/Interfaces/Race';
import RaceResult from '@/Interfaces/RaceResult';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { onMounted } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Series from '@/Interfaces/Series';

interface Results {
    [key: number]: RaceResult;
}

interface Entrant {
    name: string,
    number: number,
    style_string: string,
    accent_colour: string,
    first?: boolean,
    results: Results,
}

interface Entrants {
    [key: string]: Entrant,
}

interface Driver {
    name: string,
    points: number,
    position: number,
    rowspan?: number,
    entrants: Entrants,
}

interface Standings {
    [key: string]: Driver,
}

interface Props {
    season: SeasonInterface,
    series: Series,
    races: Race[],
    standings: Standings,
}

const props = defineProps<Props>();

const lastPointPayingPosition = props.season.last_point_paying_position;

const getResultDisplayClasses = (result: RaceResult): string => {
    return getResultClasses(result, lastPointPayingPosition);
};

onMounted(() => {
    for (const [ index, driver ] of Object.entries(props.standings)) {
        driver.rowspan = Object.keys(driver.entrants).length;

        const firstEntrant = Object.keys(driver.entrants).at(0);
        driver.entrants[firstEntrant].first = true;
    }
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
