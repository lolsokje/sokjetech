<template>
    <Breadcrumb :link="route('seasons.races.index', race.season)"
                :linkText="race.season.full_name"
                :label="race.name"
                append="Qualifying"
    />

    <div class="alert bg-danger text-white container w-50" v-if="qualifyingStore.error">
        Something went wrong saving the results, please refresh the page and try again.
    </div>

    <ThreeSessionElimination ref="qualifyingComponent" v-if="isMultiSession"/>
    <SingleSessionQualifying ref="qualifyingComponent" v-else/>

    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { Race } from '@/Interfaces/Race';
import { onMounted, ref } from 'vue';
import Permission from '@/Interfaces/Permission';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { qualifyingStore } from '@/Stores/qualifyingStore';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import QualifyingResult from '@/Interfaces/Race/QualifyingResult';
import SingleSessionQualifying from '@/Components/RaceWeekend/Qualifying/SingleSessionQualifying.vue';
import { getBestRuns, sortDriversByPosition } from '@/Composables/useQualifying';
import ThreeSessionElimination from '@/Components/RaceWeekend/Qualifying/ThreeSessionElimination.vue';

interface Props {
    race: Race,
    results: QualifyingResult[],
    sessions: number,
    can: Permission,
}

const props = defineProps<Props>();

const qualifyingComponent = ref();

const isMultiSession = props.sessions > 1;

onMounted(() => {
    qualifyingStore.race = props.race;
    qualifyingStore.results = props.results;
    qualifyingStore.activeDrivers = props.results;
    qualifyingStore.formatDetails = props.race.season.format;
    qualifyingStore.canRunQualifying = props.can.edit ?? false;
    qualifyingStore.currentSession = props.race.qualifying_session;
    qualifyingStore.totalSessions = props.sessions;
    qualifyingStore.currentRun = props.race.qualifying_run;

    if (qualifyingStore.totalSessions > 1) {
        qualifyingComponent.value.setActiveDrivers();
    }

    sortDriversByPosition();
    getBestRuns();
});
</script>

<script lang="ts">
import RaceWeekend from '@/Layouts/RaceWeekend.vue';

export default { layout: RaceWeekend };
</script>
