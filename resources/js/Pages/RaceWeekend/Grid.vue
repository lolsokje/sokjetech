<template>
    <InertiaLink :href="route('weekend.race', race)" class="btn btn-primary mb-3">Go to race</InertiaLink>

    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Starting grid"
    />

    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ race.order }} - {{ race.name }}</h2>
            <h2 class="ms-auto">
                Starting Grid
            </h2>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Pos</th>
                <th class="colour-accent"></th>
                <th>Driver</th>
                <th class="text-center">#</th>
                <th>Team</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in drivers" :key="driver.id">
                <td class="small-centered">{{ driver.result.position }}</td>
                <BackgroundColourCell :backgroundColour="driver.team.background_colour"/>
                <td>
                    <DriverName :firstName="driver.driver.first_name" :lastName="driver.driver.last_name"/>
                </td>
                <DriverNumberCell :number="driver.driver.number" :styleString="driver.team.style_string"/>
                <td class="padded-left">{{ driver.team.team_name }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Race } from '@/Interfaces/Race';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import DriverName from '@/Components/DriverName.vue';

interface StartingGridDriver {
    id: string,
    driver: {
        first_name: string,
        last_name: string,
        number: number,
    },
    team: {
        team_name: string,
        style_string: string,
        background_colour: string,
    },
    result: {
        position: number,
    }
}

interface Props {
    race: Race,
    drivers: StartingGridDriver[],
}

const props = defineProps<Props>();
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
