<template>
    <h3>Global teams database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sortTable(params, 'full_name')">
                <span>Full name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
            </th>
            <th role="button" @click="sortTable(params, 'short_name')">
                <span>Short name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="short_name"/>
            </th>
            <th role="button" @click="sortTable(params, 'team_principal')">
                <span>Team principal</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="team_principal"/>
            </th>
            <th class="text-center" role="button" @click="sortTable(params, 'country')">
                Country
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="team in teams" :key="team.id">
            <td class="padded-left">{{ team.full_name }}</td>
            <td class="padded-left">{{ team.short_name }}</td>
            <td class="padded-left">{{ team.team_principal }}</td>
            <td class="smallest-centered">
                <CountryFlag :country="team.country"/>
            </td>
            <td class="small-centered">
                <button class="btn btn-link" @click.prevent="showDialog(team)">copy</button>
            </td>
        </tr>
        </tbody>
    </table>
    <Pagination :links="links"/>

    <CustomModal id="copyTeamModal">
        <template #header>
            <h5 class="modal-title">Copy team</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </template>
        <template #default>
            <form method="POST">
                <div class="mb-3">
                    <label for="universe" class="form-label">Universe to copy to</label>
                    <select id="universe" class="form-select" v-model="form.universe_id">
                        <option v-for="universe in universes" :key="universe.id" :value="universe.id">
                            {{ universe.name }}
                        </option>
                    </select>
                </div>
            </form>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button"
                    class="btn btn-primary"
                    @click.prevent="copy()"
                    :disabled="form.universe_id === null || form.processing"
            >
                Copy Team
            </button>
        </template>
    </CustomModal>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { filter, sortTable } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import { Modal } from 'bootstrap';
import { useForm } from '@inertiajs/vue3';
import CustomModal from '@/Components/Modal.vue';

const props = defineProps({
    teams: Array,
    links: Array,
    filters: Object,
    universes: Array,
});

const modal = ref(null);
const selectedTeam = ref(null);

const params = reactive({
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    search: props.filters.search ?? '',
});

const form = useForm({
    universe_id: null,
});

const showDialog = (team) => {
    selectedTeam.value = team;
    modal.value.show();
};

const copy = async () => {
    form.post(route('database.teams.copy', selectedTeam.value), {
        preserveScroll: true,
        onSuccess: () => modal.value.hide(),
    });
};

watch(params, () => {
    filter(params, route('database.teams.index'));
});

onMounted(() => {
    modal.value = new Modal('#copyTeamModal');
});
</script>

<script>
import Database from '@/Layouts/Database.vue';

export default { layout: Database };
</script>
