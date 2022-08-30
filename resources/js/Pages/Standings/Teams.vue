<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <h3>Team standings</h3>

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
        <template v-for="(team, index) in teams" :key="team.id">
            <tr v-for="(driver, driverIndex) in team.results" :key="driver.id">
                <template v-if="isFirstResult(team, driverIndex)">
                    <td class="small-centered" :rowspan="team.driver_count">
                        {{ index + 1 }}
                    </td>
                    <BackgroundColourCell :backgroundColour="team.background_colour" :rowspan="team.driver_count"/>
                    <td class="padded-left" :rowspan="team.driver_count">{{ team.team_name }}</td>
                    <td class="medium-centered" :rowspan="team.driver_count">{{ team.points }}</td>
                </template>
                <td class="small-centered" :style="team.style_string">{{ driver.number }}</td>
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

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { getResultClasses } from '@/Composables/useResultPage';
import { getTeamPoints, sortResults } from '@/Composables/useChampionshipStandings';
import BackgroundColourCell from '@/Components/BackgroundColourCell';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';

const props = defineProps({
    season: Object,
    races: Array,
    teams: Array,
});

const isFirstResult = (team, resultId) => {
    const results = team.results;
    const firstResultId = Object.keys(results)[0];

    return firstResultId === resultId;
};

const getResultDisplayClasses = (result) => {
    return getResultClasses(result);
};

onMounted(() => {
    getTeamPoints(props.teams);

    sortResults(props.teams);
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
