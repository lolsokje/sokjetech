<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your runs. Please refresh the page and try again.
    </div>

    <component
        :is="qualifyingComponent"
        :formatDetails="race.season.format"
        :drivers="sortedDrivers"
        :canRunQualifying="can.edit"
        :results="qualifyingResults"
        :sessionDetails="race.qualifying_details"
        :completed="race.qualifying_completed"
        :showError="showError"
        :saving="saving"
        @runPerformed="storeQualifyingResult"
        @completeQualifying="completeQualifying"
    />
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { markRaw, onMounted, ref } from 'vue';
import { getQualifyingFormatComponentName } from '@/Composables/useQualifyingFormat';
import ThreeSessionElimination from '@/Components/RaceWeekend/ThreeSessionElimination';
import SingleSession from '@/Components/RaceWeekend/SingleSession';
import { Inertia } from '@inertiajs/inertia';
import { sortDriversByTotal } from '@/Composables/useRunQualifying';
import axios from 'axios';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
    qualifyingResults: {
        type: Object,
        required: false,
    },
});

const showError = ref(false);
const saving = ref(false);

const components = {
    three_session_elimination: ThreeSessionElimination,
    single_session: SingleSession,
};

const qualifyingComponent = ref(null);
const format = ref(null);
const sortedDrivers = ref(null);

onMounted(() => {
    format.value = getQualifyingFormatComponentName(props.race.season.format_type);
    qualifyingComponent.value = markRaw(components[format?.value]);

    sortedDrivers.value = props.drivers;
});

const storeQualifyingResult = (data) => {
    saving.value = true;
    const details = data.details;
    const drivers = [];

    data.results.forEach(result => {
        drivers.push({
            id: result.id,
            entrant_id: result.entrant_id,
            driver_rating: result.driver_rating,
            team_rating: result.team_rating,
            engine_rating: result.engine_rating,
            runs: result.runs,
        });
    });

    sortDriversByTotal(drivers);

    drivers.forEach((driver, index) => {
        driver.position = index + 1;
    });

    axios.post(route('weekend.qualifying.results.store', [ props.race ]), { drivers, details })
        .catch(() => showError.value = true)
        .finally(() => saving.value = false);
};

const completeQualifying = () => {
    raceWeekendStore.completeQualifying();
    Inertia.post(route('weekend.qualifying.complete', [ props.race ]));
};
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
