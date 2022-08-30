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
        <tr v-for="(driver, index) in drivers" :key="driver.id">
            <td class="small-centered">{{ index + 1 }}</td>
            <BackgroundColourCell :backgroundColour="driver.background_colour"/>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="small-centered" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td class="medium-centered">{{ driver.points }}</td>
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

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { getResultClasses } from '@/Composables/useResultPage';
import { getDriverPoints, sortResults } from '@/Composables/useChampionshipStandings';
import BackgroundColourCell from '@/Components/BackgroundColourCell';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';

const props = defineProps({
    season: Object,
    races: Array,
    drivers: Array,
});

const lastPointPayingPosition = props.season.last_point_paying_position;

const getResultDisplayClasses = (result) => {
    return getResultClasses(result, lastPointPayingPosition);
};

onMounted(() => {
    getDriverPoints(props.drivers);

    sortResults(props.drivers);
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
