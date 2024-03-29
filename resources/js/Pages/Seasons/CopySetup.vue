<template>
    <Breadcrumb :link="route('seasons.races.index', season)" :linkText="season.full_name" label="Copy setup"/>

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

        <template v-if="selectedSeason">
            <div v-if="driversChecked">
                <p class="text-danger w-25">
                    When copying drivers, copying of teams is mandatory to ensure all copied drivers
                    actually belong to a team in the new season.
                </p>
            </div>

            <div class="mb-5" v-for="(item, index) in state" :key="index">
                <input type="checkbox"
                       v-model="item.checked"
                       class="form-check-inline"
                       :id="index"
                       :disabled="isCopying || (item.entity === 'teams' && driversChecked)"
                >
                <fa icon="check" class="me-3" v-if="item.completed"/>
                <label :for="index" class="form-label">{{ item.label }}</label>

                <div v-for="(dependency, index) in item.dependencies" :key="index">
                    <input type="checkbox"
                           v-model="dependency.checked"
                           class="form-check-inline"
                           :id="`${item.entity}_${dependency.name}`"
                           :disabled="!item.checked || isCopying"
                    >
                    <fa icon="check" class="me-3" v-if="dependency.checked && item.completed"/>
                    <label :for="`${item.entity}_${dependency.name}`">{{ dependency.label }}</label>
                </div>

                <p class="text-danger" v-if="item.fail">{{ item.error }}</p>
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
import { computed, onMounted, reactive, ref, watch } from 'vue';
import CopySeasonSetupItem from '@/Utilities/CopySeasonSetupItem';
import CopySeasonSetupItemDependency from '@/Utilities/CopySeasonSetupItemDependency';
import axios from 'axios';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    season: Object,
});

const seasons = props.season.series.seasons;
const availableSeasons = ref([]);
const selectedSeason = ref("");
const totalItems = ref(0);
const completedItems = ref(0);

const state = reactive({
    copyEntrants: new CopySeasonSetupItem(
        'Copy teams and engines?',
        'teams',
        new CopySeasonSetupItemDependency('copy_ratings', 'Copy ratings (including reliability)?'),
    ),
    copyDrivers: new CopySeasonSetupItem(
        'Copy drivers?',
        'drivers',
        new CopySeasonSetupItemDependency('copy_ratings', 'Copy ratings (including reliability)?'),
    ),
    copyRaces: new CopySeasonSetupItem('Copy races?', 'races', new CopySeasonSetupItemDependency('copy_stints', 'Copy race stints?')),
    copyQualifyingFormat: new CopySeasonSetupItem('Copy qualifying format?', 'qualifying'),
    copyPointSystem: new CopySeasonSetupItem('Copy point system?', 'points'),
    copyReliabilityConfiguration: new CopySeasonSetupItem('Copy reliability configuration?', 'reliability'),
});

const canCopy = computed(() => Object.values(state).some(item => item.checked));
const isCopying = computed(() => Object.values(state).some(item => item.copying));

const startCopying = async () => {
    const items = Object.values(state).filter(item => item.checked);
    totalItems.value = items.length;
    completedItems.value = 0;

    for (const item of items) {
        item.completed = false;
        item.copying = true;

        const data = {
            season_id: selectedSeason.value,
        };

        item.dependencies.forEach((dependency) => {
            data[dependency.name] = dependency.checked;
        });

        await axios.post(route(`seasons.settings.copy.${item.entity}`, [ props.season ]), data)
            .then(() => {
                item.completed = true;
                completedItems.value++;
            })
            .catch((error) => {
                item.fail = true;
                item.error = error.response.data.error;
            })
            .finally(() => {
                item.copying = false;
            });
    }
};

const driversChecked = computed(() => {
    return state.copyDrivers.checked;
});

watch(state, () => {
    // make sure entrants are always copied when copying drivers
    if (driversChecked.value) {
        state.copyEntrants.checked = true;
    }
});

onMounted(() => {
    availableSeasons.value = seasons.filter(season => season.id !== props.season.id);
});
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
