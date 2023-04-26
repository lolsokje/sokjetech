<template>
    <Breadcrumb :link="route('universes.series.index', series)" :linkText="series.name" label="Seasons"/>

    <InertiaLink v-if="can.edit" :href="route('series.seasons.create', [series])" class="btn btn-primary my-3">
        Create season
    </InertiaLink>

    <table class="table table-narrow">
        <thead>
        <tr>
            <th>Season</th>
            <th :colspan="can.edit ? 3 : 1"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="season in series.seasons" :key="season.id">
            <td class="padded-left">{{ season.full_name }}</td>
            <td class="small-centered" v-if="can.edit">
                <InertiaLink :href="route('series.seasons.edit', [series, season])">edit</InertiaLink>
            </td>
            <td class="small-centered">
                <InertiaLink :href="route('series.seasons.show', [series, season])">view</InertiaLink>
            </td>
            <td class="medium-centered" v-if="can.edit">
                <button class="btn btn-link" @click="deleteSeason(season)">delete</button>
            </td>
        </tr>
        </tbody>
    </table>

    <CustomModal id="deleteSeasonModal">
        <template #header>
            <h2>Are you sure you want to delete this season?</h2>
        </template>
        <template v-if="seasonToDelete" #default>
            <p>
                Deleting a season is irreversible and permanent, are you absolutely sure you want to delete
                "{{ seasonToDelete.full_name }}"?
            </p>

            <label for="confirmation" class="form-label">Please type "{{ seasonToDelete.full_name }}" to confirm</label>
            <input type="text" v-model="confirmationText" id="confirmation" class="form-control">
        </template>
        <template #footer>
            <button class="btn btn-secondary me-auto" @click="closeModal()">
                Cancel
            </button>
            <button @click="confirmDeletion()"
                    :disabled="confirmationText !== seasonToDelete?.full_name"
                    class="btn btn-danger ms-auto"
            >
                Confirm
            </button>
        </template>
    </CustomModal>
</template>

<script setup lang="ts">
import Season from '@/Interfaces/Season';
import { onMounted, ref, Ref } from 'vue';
import { Modal } from 'bootstrap';
import CustomModal from '@/Components/Modal.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    series: Series,
    can: Permission,
}

const props = defineProps<Props>();

const seasonToDelete: Ref<Season | null> = ref(null);
const deleteModal = ref(null);
const confirmationText: Ref<string> = ref('');

const deleteSeason = (season: Season): void => {
    seasonToDelete.value = season;

    deleteModal.value.show();
};

const confirmDeletion = (): void => {
    axios.delete(route('series.seasons.destroy', [ props.series, seasonToDelete.value.id ]))
        .then(() => {
            router.reload({ only: [ 'series' ] });
            closeModal();
        });
};

const closeModal = (): void => {
    deleteModal.value.hide();
    seasonToDelete.value = null;
    confirmationText.value = '';
};

onMounted(() => {
    deleteModal.value = new Modal('#deleteSeasonModal');
});
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
