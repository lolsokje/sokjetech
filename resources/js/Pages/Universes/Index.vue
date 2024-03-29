<template>
    <Breadcrumb :link="route('index')" linkText="Home" label="Universes"/>

    <InertiaLink :href="route('universes.create')" class="btn btn-primary my-3" v-if="user">Add universe</InertiaLink>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">
    <div class="form-check-inline mb-3" v-if="user">
        <input id="edit-mode" v-model="params.mine" class="form-check-inline" type="checkbox">
        <label class="form-check-label" for="edit-mode">Show only my universes</label>
    </div>
    <template v-if="universes.length">
        <table class="table table-narrow">
            <thead>
            <tr>
                <th role="button" @click="sortTable(params)">
                    Name
                    <OrderIcon :direction="params.direction"/>
                </th>
                <th>User</th>
                <th colspan="2"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="universe in universes" v-bind:key="universe.id">
                <td class="padded-left">{{ universe.name }}</td>
                <td class="padded-left">{{ universe.user.username }}</td>
                <td class="small-centered">
                    <InertiaLink v-if="universe.can?.edit" :href="route('universes.edit', universe)">edit</InertiaLink>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('universes.series.index', universe)">view</InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No universes found</p>
</template>

<script setup lang="ts">
import { computed, reactive, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Pagination from '@/Shared/Pagination.vue';
import { filter, sortTable } from '@/Composables/useTableFiltering.js';
import OrderIcon from '@/Shared/OrderIcon.vue';
import PaginationLink from '@/Interfaces/PaginationLink';
import Filters from '@/Interfaces/Filters';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    links: Array<PaginationLink>,
    filters: Filters,
    universes: Array<Universe>,
}

const props = defineProps<Props>();

const params = reactive({
    search: props.filters.search ?? '',
    direction: props.filters.direction ?? '',
    mine: props.filters.mine ?? false,
});

const user = computed((): User => usePage().props.auth.user);

watch(params, (): void => {
    filter(params, route('universes.index'));
});
</script>
