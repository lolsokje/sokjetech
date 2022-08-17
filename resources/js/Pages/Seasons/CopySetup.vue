<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <div class="alert alert-danger mb-3">
        Copying configuration from another season will completely overwrite that specific configuration from the current
        season.
    </div>
    <template v-if="!availableSeasons.length">
        <span class="text-danger">No other seasons exist in this series</span>
    </template>
    <template v-else>
        <div class="mb-3 col-3">
            <label for="selectedSeason" class="form-label">Select a season to copy from</label>
            <select v-model="selectedSeason" id="selectedSeason" class="form-select" :disabled="isCopying">
                <option v-for="availableSeason in availableSeasons" :key="availableSeason.id"
                        :value="availableSeason.id"
                >
                    {{ availableSeason.year }}
                </option>
            </select>
        </div>

        <div class="progress w-25" v-if="isCopying">
            <div class="progress-bar"
                 role="progressbar"
                 :style="`width: ${completedPercentage}%`"
                 aria-valuenow="25"
                 aria-valuemin="0"
                 aria-valuemax="100"
            ></div>
        </div>

        <template v-if="selectedSeason">
            <div class="mb-5" v-for="(item, index) in state" :key="index">
                <input type="checkbox"
                       v-model="item.checked"
                       class="form-check-inline"
                       :id="index"
                       :disabled="isCopying"
                >
                <label :for="index" class="form-label">{{ item.label }}</label>

                <div v-if="item.dependency">
                    <input type="checkbox"
                           v-model="item.dependency.checked"
                           class="form-check-inline"
                           :id="item.dependency.name"
                           :disabled="!item.checked || isCopying"
                    >
                    <label :for="item.dependency.name">{{ item.dependency.label }}</label>
                </div>
            </div>
        </template>

        <button type="button"
                class="btn btn-primary"
                :disabled="!selectedSeason || !canCopy || isCopying"
                @click.prevent="startCopying"
        >
            Copy setup
        </button>
    </template>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { computed, onMounted, reactive, ref } from 'vue';
import CopySeasonSetupItem from '@/Utilities/CopySeasonSetupItem';
import CopySeasonSetupItemDependency from '@/Utilities/CopySeasonSetupItemDependency';
import axios from 'axios';

const props = defineProps({
    season: Object,
    seasons: Array,
});

const availableSeasons = ref([]);
const selectedSeason = ref("25653579062841344");
const totalItems = ref(0);
const completedItems = ref(0);

const state = reactive({
    copyEntrants: new CopySeasonSetupItem(
        'Copy teams, drivers and engines?',
        'teams',
        new CopySeasonSetupItemDependency('copyRatings', 'Copy ratings (including reliability)?'),
        true,
    ),
    copyRaces: new CopySeasonSetupItem('Copy races?', 'races', new CopySeasonSetupItemDependency('copyStints', 'Copy race stints?')),
    copyQualifyingFormat: new CopySeasonSetupItem('Copy qualifying format?', 'format'),
    copyPointSystem: new CopySeasonSetupItem('Copy point system?', 'points'),
});

const canCopy = computed(() => Object.values(state).some(item => item.checked));
const isCopying = computed(() => Object.values(state).some(item => item.copying));
const completedPercentage = computed(() => (completedItems.value / totalItems.value) * 100);

const startCopying = async () => {
    const items = Object.values(state).filter(item => item.checked);
    totalItems.value = items.length;
    completedItems.value = 0;

    for (const item of items) {
        item.copying = true;
        await axios.post(route('seasons.settings.copy.teams', [ props.season ]));
        item.copying = false;
        item.complete = true;
        completedItems.value++;
    }
};

onMounted(() => {
    availableSeasons.value = props.seasons.filter(season => season.id !== props.season.id);
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
