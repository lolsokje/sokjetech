<template>
    <div class="container pb-3 mb-3 border-bottom border-secondary">
        <Breadcrumb :link="route('universes.drivers.index', universe)"
                    :linkText="universe.name"
                    :label="driver.full_name"
                    append="Career"
        />

        <div class="row">
            <div class="col-2">
                <div class="d-inline-flex">
                    <h5 class="text-muted">{{ driver.first_name }}</h5>
                </div>
                <h1>{{ driver.last_name }}</h1>
            </div>
            <div class="col-1"></div>
            <div class="col-9">
                <div class="row">
                    <div class="col-4 mb-3" v-for="(stat, index) in baseStats" :key="index">
                        <p class="text-muted mb-0 text-uppercase">{{ stat.label }}</p>
                        <div class="driver-stat py-2">
                            {{ stat.value }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Career results</h2>
    </div>

    <!-- TODO per-entrant results -->
    <div v-for="(seriesResults, series) in combinedStats" :key="series">
        <div class="text-center my-3">
            <h4>{{ series }}</h4>
        </div>
        <table class="table driver-stat-container">
            <thead>
            <tr>
                <th class="text-center">Year</th>
                <th class="text-center">Pos</th>
                <th class="text-center"
                    v-for="round in [...Array(getHighestRoundCountForSeries(seriesResults)).keys()]"
                    :key="round"
                >
                    {{ round + 1 }}
                </th>
                <th class="text-center">Points</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(seasonResults, year) in seriesResults" :key="year">
                <td class="small-centered">{{ year }}</td>
                <td class="smallest-centered">{{ seasonResults.position }}</td>
                <!-- TODO get result per round -->
                <td v-for="round in [...Array(getHighestRoundCountForSeries(seriesResults)).keys()]"
                    :key="round"
                    class="small-centered"
                    :class="getResultDisplayClasses(seasonResults.races[round +1])"
                >
                    {{ getResultForRound(seasonResults.races[round + 1]) }}
                </td>
                <td class="small-centered">{{ seasonResults.points }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts" setup>
import Universe from '@/Interfaces/Universe';
import Driver from '@/Interfaces/Driver';
import axios from 'axios';
import { onMounted, Ref, ref } from 'vue';
import { getResultClasses } from '@/Composables/useResultPage.js';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    universe: Universe,
    driver: Driver,
}

interface BaseStats {
    [key: string]: number,
}

interface SeasonResult {
    id: number,
    race_id: string,
    name: string,
    round: number,
    year: number,
    circuit_country: string,
    starting_position: number,
    position: number,
    fastest_lap: boolean,
    dnf: string | null,
    points: number,
}

interface SeriesResults {
    [key: number]: SeasonResult[],
}

interface CombinedStats {
    [key: string]: SeriesResults[],
}

const props = defineProps<Props>();

const baseStats: Ref<BaseStats[]> = ref([]);
const combinedStats: Ref<CombinedStats | null> = ref(null);

const getBaseStats = async () => {
    const response = await axios.get(route('drivers.stats.base', [ props.driver ]));
    baseStats.value = await response.data as BaseStats[];
};

const getCombinedStats = async () => {
    const response = await axios.get(route('drivers.stats.combined', [ props.driver ]));
    combinedStats.value = await response.data as CombinedStats;
};

const getResultForRound = (result: SeasonResult | null): number | string | null => {
    if (! result) {
        return null;
    }

    return result.dnf ? 'DNF' : result.position;
};

const getTotalPointsScoredInSeason = (results: SeasonResult[]): number => {
    return Object.values(results).reduce((acc: number, currentValue: SeasonResult) => acc + currentValue.points, 0);
};

const getResultDisplayClasses = (result) => {
    return result ? getResultClasses(result, 3) : '';
};

const getHighestRoundCountForSeries = (results) => {
    let mostRounds = 0;
    for (const season of Object.values(results)) {
        mostRounds = Math.max(Object.keys(season.races).length, mostRounds);
    }

    return mostRounds;
};

onMounted(() => {
    getBaseStats();
    getCombinedStats();
});
</script>

<style lang="scss" scoped>
.driver-stat {
    text-align: center;
    border: 1px solid var(--primary);
    background-color: var(--base-100);
}

.driver-stat-container {
    max-width: 2000px;
    margin: 0 auto;
}

.table {
    width: auto;
}
</style>
