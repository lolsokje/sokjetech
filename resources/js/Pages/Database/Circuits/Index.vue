<template>
    <h3>Global circuits database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sortTable(params, 'name')">
                <span>Name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
            </th>
            <th class="text-center" role="button" @click="sortTable(params, 'country')">
                Country
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="circuit in circuits" :key="circuit.id">
            <td class="padded-left">{{ circuit.name }}</td>
            <td class="smallest-centered">
                <CountryFlag :country="circuit.country"/>
            </td>
            <td class="small-centered">
                <button class="btn btn-link" @click.prevent="copy(circuit)">copy</button>
            </td>
        </tr>
        </tbody>
    </table>
    <Pagination :links="links"/>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { filter, sortTable } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    circuits: Array,
    links: Array,
    filters: Object,
});

const params = reactive({
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    search: props.filters.search ?? '',
});

const copy = (circuit) => {
    if (! confirm('Are you sure you want to copy this circuit so you can use it for yourself?')) {
        return;
    }

    router.post(route('database.circuits.copy', circuit), {}, {
        preserveScroll: true,
    });
};

watch(params, () => {
    filter(params, route('database.circuits.index'));
});
</script>

<script>
import Database from '@/Layouts/Database.vue';

export default { layout: Database };
</script>
