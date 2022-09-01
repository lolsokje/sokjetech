<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="d-flex col-6 mb-3">
        <h3>{{ race.name }} Starting Grid</h3>
        <InertiaLink :href="route('weekend.race', race)" class="btn btn-primary ms-auto">Go to race</InertiaLink>
    </div>

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
            <td class="small-centered">{{ driver.position }}</td>
            <BackgroundColourCell :backgroundColour="driver.background_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td :style="driver.style_string" class="small-centered">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { sortDriversByPosition } from '@/Composables/useRunQualifying';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';
import BackgroundColourCell from '@/Components/BackgroundColourCell';

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
import RaceWeekend from '@/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
