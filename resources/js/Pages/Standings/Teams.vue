<template>
    <Breadcrumb :link="route('series.seasons.index', series)"
                :linkText="series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Team standings"
    />

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="colour-accent"></th>
            <th>TEAM</th>
            <th class="text-center">PTS</th>
            <th class="text-center"></th>
            <th class="colour-accent"></th>
            <th v-for="race in races" :key="race.order" class="text-center">
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
        <template v-for="team in teams" :key="team.id">
            <tr v-for="(driver, driverIndex) in team.results" :key="driver.id">
                <template v-if="isFirstResult(team, driverIndex)">
                    <td class="smallest-centered" :rowspan="team.driver_count">
                        {{ team.position }}
                    </td>
                    <BackgroundColourCell :backgroundColour="team.background_colour" :rowspan="team.driver_count"/>
                    <td class="padded-left" :rowspan="team.driver_count">{{ team.full_name }}</td>
                    <td class="small-centered" :rowspan="team.driver_count">{{ team.points }}</td>
                </template>
                <DriverNumberCell :number="driver.number" :styleString="team.style_string"/>
                <td class="colour-accent"></td>
                <td v-for="race in races" :key="race.order" class="smallest-centered"
                    :class="getResultDisplayClasses(driver.results[race.order])"
                >
                    {{ driver.results[race.order]?.position }}
                </td>
            </tr>
        </template>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { getResultClasses } from '@/Composables/useResultPage.js';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import SeasonInterface from '@/Interfaces/Season';
import { Race } from '@/Interfaces/Race';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Series from '@/Interfaces/Series';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';

interface DriverRaceResult {
    dnf: string | null,
    fastest_lap: boolean,
    points: number,
    position: number | string,
    starting_position: number,
}

interface Racer {
    number: number,
    results: DriverRaceResult[],
}

interface TeamChampionshipStanding {
    id: string,
    full_name: string,
    points: number,
    position: number,
    team_name: string,
    team_principal: string,
    background_colour: string,
    style_string: string,
    driver_count: number,
    results: {
        [key: string]: Racer
    }
}

interface Props {
    season: SeasonInterface,
    series: Series,
    races: Race[],
    teams: TeamChampionshipStanding[],
}

const props = defineProps<Props>();

const lastPointPayingPosition = props.season.last_point_paying_position;

const isFirstResult = (team: TeamChampionshipStanding, resultId: string): boolean => {
    const results = team.results;
    const firstResultId = Object.keys(results)[0];

    return firstResultId === resultId;
};

const getResultDisplayClasses = (result: DriverRaceResult): string => {
    return getResultClasses(result, lastPointPayingPosition);
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
