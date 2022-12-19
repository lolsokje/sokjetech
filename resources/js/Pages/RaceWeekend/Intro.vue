<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <InertiaLink :href="route('weekend.qualifying', race)" class="btn btn-primary">
        Go to qualifying
    </InertiaLink>
    <div id="screenshot-target">
        <h3>Weekend intro</h3>

        <h4>Stints</h4>

        <table class="table">
            <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">Use driver rating</th>
                <th class="text-center">Use team rating</th>
                <th class="text-center">Use engine rating</th>
                <th class="text-center">Reliability rolls</th>
                <th class="text-center">Min RNG</th>
                <th class="text-center">Max RNG</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="stint in stints" :key="stint.order">
                <td class="smallest-centered">{{ stint.order }}</td>
                <td class="text-center">
                    <fa :icon="getIcon(stint.use_driver_rating)"/>
                </td>
                <td class="text-center">
                    <fa :icon="getIcon(stint.use_team_rating)"/>
                </td>
                <td class="text-center">
                    <fa :icon="getIcon(stint.use_engine_rating)"/>
                </td>
                <td class="text-center">
                    <fa :icon="getIcon(stint.reliability)"/>
                </td>
                <td class="text-center">{{ stint.min_rng }}</td>
                <td class="text-center">{{ stint.max_rng }}</td>
            </tr>
            </tbody>
        </table>

        <h4>Standings</h4>
        <div class="row mt-3">
            <div class="col-6">
                <h5>Driver standings</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="colour-accent"></th>
                        <th>Driver</th>
                        <th></th>
                        <th>Team</th>
                        <th class="text-center">Points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(driver, index) in driverStandings" :key="index">
                        <td class="smallest-centered">{{ index + 1 }}</td>
                        <BackgroundColourCell :backgroundColour="driver.background_colour"/>
                        <td class="padded-left">{{ driver.full_name }}</td>
                        <td class="smallest-centered" :style="driver.style_string">{{ driver.number }}</td>
                        <td class="padded-left">{{ driver.team_name }}</td>
                        <td class="smallest-centered">{{ driver.points }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <h5>Team standings</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="colour-accent"></th>
                        <th>Team name</th>
                        <th>Team principal</th>
                        <th class="text-center">Points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(team, index) in teamStandings" :key="index">
                        <td class="smallest-centered">{{ index + 1 }}</td>
                        <BackgroundColourCell :backgroundColour="team.background_colour"/>
                        <td class="padded-left">{{ team.full_name }}</td>
                        <td class="padded-left">{{ team.team_principal }}</td>
                        <td class="smallest-centered">{{ team.points }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import { onMounted } from 'vue';
import {
    getDriverPoints,
    getTeamPoints,
    getTopPerformers,
    sortResults,
} from '@/Composables/useChampionshipStandings.js';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import { Race } from '@/Interfaces/Race';
import RaceStint from '@/Interfaces/RaceStint';
import RaceResult from '@/Interfaces/RaceResult';
import TeamRaceResult from '@/Interfaces/TeamRaceResult';

interface DriverStandings {
    id: string,
    full_name: string,
    team_name: string,
    number: number,
    background_colour: string,
    style_string: string,
    points: number,
    results: RaceResult[],
}

interface TeamStandings {
    id: string,
    full_name: string,
    team_name: string,
    team_principal: string,
    driver_count: number,
    background_colour: string,
    style_string: string,
    points: number,
    results: TeamRaceResult[],
}

interface Props {
    race: Race,
    stints: RaceStint[],
    driverStandings: DriverStandings[],
    teamStandings: TeamStandings[],
}

const props = defineProps<Props>();

const getIcon = (boolean: boolean): string => {
    return boolean ? 'check' : 'times';
};

onMounted(() => {
    getDriverPoints(props.driverStandings);
    sortResults(props.driverStandings);
    getTopPerformers(props.driverStandings, 3);

    getTeamPoints(props.teamStandings);
    sortResults(props.teamStandings);
    getTopPerformers(props.teamStandings, 3);
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
