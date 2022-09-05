<template>
    <h3>Global drivers database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sort(params, 'first_name')">
                <span>Full name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
            </th>
            <th role="button" @click="sort(params, 'dob')">
                <span>Date of birth</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="dob"/>
            </th>
            <th class="text-center" role="button" @click="sort(params, 'country')">
                Country
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="driver in drivers" :key="driver.id">
            <td class="padded-left">{{ driver.full_name }}</td>
            <td class="padded-left">{{ driver.readable_dob }}</td>
            <td class="smallest-centered">
                <CountryFlag :country="driver.country"/>
            </td>
            <td class="small-centered">
                <button class="btn btn-link" @click.prevent="showDialog(driver)">copy</button>
            </td>
        </tr>
        </tbody>
    </table>
    <Pagination :links="links"/>

    <CustomModal id="copyDriverModal">
        <template #header>
            <h5 class="modal-title">Copy driver</h5>
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
                Copy Driver
            </button>
        </template>
    </CustomModal>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { filter, sort } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon';
import Pagination from '@/Shared/Pagination';
import { Modal } from 'bootstrap';
import { useForm } from '@inertiajs/inertia-vue3';
import CustomModal from '@/Components/Modal';

const props = defineProps({
    drivers: Array,
    links: Array,
    filters: Object,
    universes: Array,
});

const modal = ref(null);
const selectedDriver = ref(null);

const params = reactive({
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    search: props.filters.search ?? '',
});

const form = useForm({
    universe_id: null,
});

const showDialog = (driver) => {
    selectedDriver.value = driver;
    modal.value.show();
};

const copy = async () => {
    form.post(route('database.drivers.copy', selectedDriver.value), {
        preserveScroll: true,
        onSuccess: () => modal.value.hide(),
    });
};

watch(params, () => {
    filter(params, route('database.drivers.index'));
});

onMounted(() => {
    modal.value = new Modal('#copyDriverModal');
});
</script>

<script>
import Database from '@/Layouts/Database';

export default { layout: Database };
</script>
