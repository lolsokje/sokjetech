<template>
    <h1>Circuits</h1>

    <InertiaLink :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</InertiaLink>

    <InertiaLink :href="route('database.circuits.index')" class="text-decoration-underline ms-3">
        or copy an existing circuit
    </InertiaLink>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">

    <template v-if="circuits.length">
        <table class="table">
            <thead>
            <tr>
                <th role="button" @click="sortTable(params, 'name')">
                    <span>Name</span>
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
                </th>
                <th role="button" @click="sortTable(params, 'country')">
                    <span>Country</span>
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
                </th>
                <th colspan="2"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="circuit in circuits" v-bind:key="circuit.id">
                <td class="padded-left">{{ circuit.name }}</td>
                <td class="small-centered">
                    <CountryFlag :country="circuit.country"/>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('circuits.edit', circuit)">edit</InertiaLink>
                </td>
                <td class="medium-centered">
                    <template v-if="circuit.races_count === 0">
                        <button class="btn btn-link" @click="deleteCircuit(circuit.id)">delete</button>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No circuits added yet</p>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import { filter, sortTable } from '@/Composables/useTableFiltering.js';
import Circuit from '@/Interfaces/Circuit';
import Filters from '@/Interfaces/Filters';
import PaginationLink from '@/Interfaces/PaginationLink';
import { router } from '@inertiajs/vue3';

interface Props {
    circuits: Array<Circuit>,
    links: Array<PaginationLink>,
    filters: Filters,
}

const props = defineProps<Props>();

const params = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

const deleteCircuit = (id: string): void => {
    if (! confirm("Are you sure you want to delete this circuit?")) {
        return;
    }

    router.delete(route('circuits.destroy', id));
};

watch(params, (): void => {
    filter(params, route('circuits.index'));
});
</script>
