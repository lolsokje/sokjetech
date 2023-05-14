<template>
    <div class="d-flex justify-content-between align-items-center">
        <Breadcrumb :link="route('seasons.races.index', race.season)"
                    :linkText="race.season_name"
                    :label="race.name"
                    append="Weekend intro"
        />

        <InertiaLink :href="route('weekend.qualifying', race)" class="btn btn-primary mb-3">
            Go to qualifying
        </InertiaLink>
    </div>

    <div id="screenshot-target">
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
                        <DriverNumberCell :number="driver.number" :styleString="driver.style_string"/>
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
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import { Race } from '@/Interfaces/Race';
import RaceStint from '@/Interfaces/RaceStint';
import DriverResults from '@/Interfaces/DriverResults';
import TeamResults from '@/Interfaces/TeamResults';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';

interface Props {
    race: Race,
    stints: RaceStint[],
    driverStandings: DriverResults[],
    teamStandings: TeamResults[],
}

const props = defineProps<Props>();

const getIcon = (boolean: boolean): string => {
    return boolean ? 'check' : 'times';
};
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
