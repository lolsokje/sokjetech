<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <h3>Driver Standings</h3>

    <table class="table table-dark table-bordered">
        <thead>
        <tr>
            <th class="text-center">POS</th>
            <th class="colour-accent"></th>
            <th>DRIVER</th>
            <th class="text-center">#</th>
            <th>TEAM</th>
            <th class="text-center">PTS</th>
            <th class="text-center" v-for="race in season.races" :key="race.order">
                <template v-if="race.completed">
                    <InertiaLink :href="route('weekend.results', [race])">
                        {{ race.order }}
                    </InertiaLink>
                </template>
                <template v-else>
                    {{ race.order }}
                </template>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(driver, index) in drivers" :key="driver.id">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="colour-accent" :style="`background-color: ${driver.background_colour}`"></td>
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="text-center" :style="driver.style_string">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td class="text-center">{{ driver.points }}</td>
            <td class="text-center" v-for="race in season.races" :key="race.order"
                :class="getResultDisplayClasses(driver.results[race.order])">
                {{ driver.results[race.order]?.position }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { getResultClasses, sortResults } from '@/Composables/useResultPage';

const props = defineProps({
    season: Object,
    drivers: Array,
});

const getResultDisplayClasses = (result) => {
    return getResultClasses(result);
};

onMounted(() => {
    props.drivers.forEach(driver => {
        let points = 0;
        Object.values(driver.results).forEach(result => points += result.points);
        driver.points = points;
    });

    sortResults(props.drivers);
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
