<template>
    <h3>Global engines database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sort(params, 'name')">
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

    <div class="modal" tabindex="-1" id="copyEngineModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy engine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button"
                            class="btn btn-primary"
                            @click.prevent="copy()"
                            :disabled="form.series_id === null || form.processing"
                    >
                        Copy Engine
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { filter, sort } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon';
import Pagination from '@/Shared/Pagination';
import { Modal } from 'bootstrap';
import { useForm } from '@inertiajs/inertia-vue3';

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
import Database from '@/Layouts/Database';

export default { layout: Database };
</script>
