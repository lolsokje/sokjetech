<template>
    <h1>Circuits</h1>

    <InertiaLink :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</InertiaLink>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">

    <template v-if="circuits.length">
        <table class="table">
            <thead>
            <tr>
                <th role="button" @click="sort(params, 'name')">
                    <span>Name</span>
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
                </th>
                <th role="button" @click="sort(params, 'country')">
                    <span>Country</span>
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
                </th>
                <th></th>
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
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No circuits added yet</p>
</template>

<script setup>
import { reactive, watch } from 'vue';
import OrderIcon from '@/Shared/OrderIcon';
import Pagination from '@/Shared/Pagination';
import { filter, sort } from '@/Composables/useTableFiltering';

const props = defineProps({
    circuits: Array,
    links: Array,
    filters: Object,
});

const params = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

watch(params, () => {
    filter(params, route('circuits.index'));
});
</script>
