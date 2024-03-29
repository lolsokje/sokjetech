<template>
    <div class="alert bg-danger text-white container w-50" v-if="showError">
        Something went wrong saving your runs. Please refresh the page and try again.
    </div>

    <component
        :is="qualifyingComponent"
        :formatDetails="race.season.format"
        :drivers="sortedDrivers"
        :canRunQualifying="can.edit"
        :sessionDetails="race.qualifying_details"
        :completed="race.qualifying_completed"
        :showError="showError"
        :saving="saving"
        :race="race"
        @runPerformed="storeQualifyingResult"
        @completeQualifying="completeQualifying"
    />
</template>

<script setup>
import { markRaw, onMounted, ref } from 'vue';
import { getQualifyingFormatComponentName } from '@/Composables/useQualifyingFormat';
import ThreeSessionElimination from '@/Components/RaceWeekend/ThreeSessionElimination.vue';
import SingleSession from '@/Components/RaceWeekend/SingleSession.vue';
import { router } from '@inertiajs/vue3';
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
            driver_rating: result.ratings.driver_rating,
            team_rating: result.ratings.team_rating,
            engine_rating: result.ratings.engine_rating,
            runs: result.result.runs,
        });
    });

    drivers.forEach((driver, index) => {
        driver.position = index + 1;
    });

    axios.post(route('weekend.qualifying.results.store', [ props.race ]), { drivers, details })
        .catch(() => showError.value = true)
        .finally(() => saving.value = false);
};

const completeQualifying = () => {
    raceWeekendStore.completeQualifying();
    router.post(route('weekend.qualifying.complete', [ props.race ]));
};
</script>

<script>
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
