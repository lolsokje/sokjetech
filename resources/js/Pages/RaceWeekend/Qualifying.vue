<template>
    <BackLink :backTo="route('seasons.races.index', [race.season])" label="race overview"/>

    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your runs. Please refresh the database and try again.
    </div>

    <component
        :is="qualifyingComponent"
        :formatDetails="race.season.format"
        :drivers="sortedDrivers"
        :canRunQualifying="can.edit"
        :results="qualifyingResults"
        :sessionDetails="race.details"
        @runPerformed="storeQualifyingResult"
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

const storeQualifyingResult = async (data) => {
    const details = data.details;
    const drivers = [];

    data.results.forEach(result => {
        drivers.push({
            id: result.id,
            runs: result.runs,
        });
    });

    sortDriversByTotal(drivers);

    drivers.forEach((driver, index) => {
        driver.position = index + 1;
    });

    Inertia.post(route('weekend.qualifying.results.store', [ props.race ]), { drivers, details }, {
        onError: () => showError.value = true,
        onSuccess: () => showError.value = false,
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<script>
import RaceWeekend from '@/Shared/Layouts/RaceWeekend';

export default { layout: RaceWeekend };
</script>
