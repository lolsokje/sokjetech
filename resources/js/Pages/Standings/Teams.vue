<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <h3>Team standings</h3>

    <table class="table table-dark table-bordered">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="colour-accent"></th>
            <th>TEAM</th>
            <th class="text-center">PTS</th>
            <th class="text-center"></th>
            <th v-for="race in season.races" :key="race.order" class="text-center">{{ race.order }}</th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(team, index) in teams" :key="team.id">
            <tr v-for="(driver, driverIndex) in team.results" :key="driver.id">
                <template v-if="isFirstResult(team, driverIndex)">
                    <td class="text-center align-middle" :rowspan="team.driver_count">
                        {{ index + 1 }}
                    </td>
                    <td class="colour-accent align-middle" :style="`background-color: ${team.background_colour}`"
                        :rowspan="team.driver_count"></td>
                    <td class="padded-left align-middle" :rowspan="team.driver_count">{{ team.team_name }}</td>
                    <td class="text-center align-middle" :rowspan="team.driver_count">{{ team.points }}</td>
                </template>
                <td class="text-center" :style="team.style_string">{{ driver.number }}</td>
                <td v-for="race in season.races" :key="race.order" class="text-center"
                    :class="getResultDisplayClasses(driver.results[race.order])">
                    {{ driver.results[race.order]?.position }}
                </td>
            </tr>
        </template>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { getResultClasses, sortResults } from '@/Composables/useResultPage';

const props = defineProps({
    season: Object,
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
    props.teams.forEach(team => {
        let points = 0;
        Object.values(team.results).forEach(drivers => {
            Object.values(drivers.results).forEach(result => points += result.points);
        });
        team.points = points;
    });

    sortResults(props.teams);
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
