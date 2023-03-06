<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <h3>Driver Standings</h3>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="colour-accent"></th>
            <th>DRIVER</th>
            <th class="text-center">#</th>
            <th>TEAM</th>
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
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="small-centered">{{ driver.position }}</td>
            <BackgroundColourCell :backgroundColour="driver.background_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="smallest-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td class="small-centered">{{ driver.points }}</td>
            <td class="smallest-centered" v-for="race in races" :key="race.order"
                :class="getResultDisplayClasses(driver.results[race.order])"
            >
                {{ driver.results[race.order]?.position }}
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import { getResultClasses } from '@/Composables/useResultPage.js';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import SeasonInterface from '@/Interfaces/Season';
import { Race } from '@/Interfaces/Race';
import RaceResult from '@/Interfaces/RaceResult';

interface RaceResult {
    dnf: string | null,
    fastest_lap: boolean,
    points: number,
    position: number | string,
    starting_position: number;
}

interface DriverChampionshipStandings {
    id: string,
    full_name: string,
    position: number,
    points: number,
    number: number,
    team_name: string,
    background_colour: string,
    style_string: string,
    results: RaceResult[],
}

interface Props {
    season: SeasonInterface,
    races: Race[],
    drivers: DriverChampionshipStandings[],
}

const props = defineProps<Props>();

const lastPointPayingPosition = props.season.last_point_paying_position;

const getResultDisplayClasses = (result: RaceResult): string => {
    return getResultClasses(result, lastPointPayingPosition);
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
