<template>
    <Breadcrumb :link="route('universes.index')" linkText="Universes" :label="universe.name" append="Drivers"/>

    <template v-if="can.edit">
        <div>
            <InertiaLink :href="route('universes.drivers.create', [universe])" class="btn btn-primary my-3">
                Add driver
            </InertiaLink>
        </div>

        <div class="mb-3">
            <InertiaLink :href="route('database.drivers.index')" class="text-decoration-underline">
                copy existing drivers
            </InertiaLink>
            &nbsp; or
            <InertiaLink :href="route('universes.drivers.generate.show', [universe])"
                         class="text-decoration-underline ms-1"
            >
                generate random drivers
            </InertiaLink>
        </div>
    </template>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">
    <template v-if="drivers.length">
        <table class="table">
            <thead>
            <tr>
                <th role="button" @click.prevent="sortTable(params, 'first_name')">
                    Driver name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="first_name"/>
                </th>
                <th role="button" @click.prevent="sortTable(params, 'dob')">
                    DOB
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="dob"/>
                </th>
                <th class="text-center">Country</th>
                <th class="text-center" role="button" @click.prevent="sortTable(params, 'retired')">
                    Retired
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="retired"/>
                </th>
                <th colspan="2"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in drivers" :key="driver.id">
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="padded-left">{{ driver.readable_dob }}</td>
                <td class="small-centered">
                    <CountryFlag :country="driver.country"/>
                </td>
                <td class="small-centered">
                    {{ driver.retired ? 'Yes' : 'No' }}
                </td>
                <td class="small-centered">
                    <InertiaLink v-if="can.edit" :href="route('universes.drivers.edit', [universe, driver])">
                        edit
                    </InertiaLink>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('universes.drivers.show', [universe, driver])">
                        view
                    </InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No drivers found </p>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';
import { filter, sortTable } from '@/Composables/useTableFiltering.js';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import Filters from '@/Interfaces/Filters';
import PaginationLink from '@/Interfaces/PaginationLink';
import Universe from '@/Interfaces/Universe';
import Driver from '@/Interfaces/Driver';
import Permission from '@/Interfaces/Permission';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    universe: Universe,
    drivers: Array<Driver>,
    filters: Filters,
    can: Permission,
    links: Array<PaginationLink>,
}

const props = defineProps<Props>();

const params = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

watch(params, (): void => {
    filter(params, route('universes.drivers.index', [ props.universe ]));
});
</script>

<script lang="ts">
import UniverseLayout from '@/Layouts/Universe.vue';

export default { layout: UniverseLayout };
</script>
