<template>
    <div class="d-flex justify-content-between align-items-center">
        <Breadcrumb :link="route('seasons.races.index', race.season)"
                    :linkText="race.season_name"
                    :label="race.name"
                    append="Weekend intro"
        />

        <InertiaLink :href="route('weekend.qualifying.start', race)"
                     as="button"
                     method="POST"
                     class="btn btn-primary mb-3"
                     v-if="!race.qualifying_started"
        >
            Start weekend
        </InertiaLink>
        <InertiaLink :href="route('weekend.qualifying', race)" class="btn btn-primary mb-3" v-else>
            Go to qualifying
        </InertiaLink>
    </div>

    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ race.order }} - {{ race.name }}</h2>
            <h2 class="ms-auto">
                Intro
            </h2>
        </div>

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
import DriverResults from '@/Interfaces/DriverResults';
import TeamResults from '@/Interfaces/TeamResults';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';

interface Props {
    race: Race,
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
