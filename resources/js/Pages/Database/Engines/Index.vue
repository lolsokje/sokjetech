<template>
    <h3>Global engines database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sortTable(params, 'name')">
                <span>Name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="engine in engines" :key="engine.id">
            <td class="padded-left">{{ engine.name }}</td>
            <td class="small-centered">
                <button class="btn btn-link" @click.prevent="showDialog(engine)">copy</button>
            </td>
        </tr>
        </tbody>
    </table>
    <Pagination :links="links"/>

    <CustomModal id="copyEngineModal">
        <template #header>
            <h5 class="modal-title">Copy engine</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </template>
        <template #default>
            <form method="POST">
                <div class="mb-3">
                    <label for="universe" class="form-label">Series to copy to</label>
                    <select id="universe" class="form-select" v-model="form.series_id">
                        <option v-for="series in series" :key="series.id" :value="series.id">
                            {{ series.name }} ({{ series.universe.name }})
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
                    :disabled="form.series_id === null || form.processing"
            >
                Copy Engine
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
    engines: Array,
    links: Array,
    filters: Object,
    series: Array,
});

const modal = ref(null);
const selectedEngine = ref(null);

const params = reactive({
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    search: props.filters.search ?? '',
});

const form = useForm({
    series_id: null,
});

const showDialog = (engine) => {
    selectedEngine.value = engine;
    modal.value.show();
};

const copy = async () => {
    form.post(route('database.engines.copy', selectedEngine.value), {
        preserveScroll: true,
        onSuccess: () => modal.value.hide(),
    });
};

watch(params, () => {
    filter(params, route('database.engines.index'));
});

onMounted(() => {
    modal.value = new Modal('#copyEngineModal');
});
</script>

<script>
import Database from '@/Layouts/Database.vue';

export default { layout: Database };
</script>
