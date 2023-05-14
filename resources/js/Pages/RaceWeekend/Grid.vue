<template>
    <InertiaLink :href="route('weekend.race', race)" class="btn btn-primary mb-3">Go to race</InertiaLink>

    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Starting grid"
    />

    <table class="table table-narrow" id="screenshot-target">
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
            <td class="padded-left">{{ driver.full_name }}</td>
            <DriverNumberCell :number="driver.number" :styleString="driver.team.style_string"/>
            <td class="padded-left">{{ driver.team.team_name }}</td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
});

onMounted(() => sortDriversByPosition(props.drivers));
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
