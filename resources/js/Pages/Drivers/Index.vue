<template>
    <BackLink :backTo="route('universes.index')" label="universe overview"/>

    <h3>Drivers</h3>

    <InertiaLink v-if="can.edit" :href="route('universes.drivers.create', [universe])" class="btn btn-primary my-3">
        Add driver
    </InertiaLink>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">
    <template v-if="drivers.length">
        <table class="table">
            <thead>
            <tr>
                <th role="button" @click.prevent="sort(params, 'first_name')">
                    Driver name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="first_name"/>
                </th>
                <th role="button" @click.prevent="sort(params, 'dob')">
                    DOB
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="dob"/>
                </th>
                <th class="text-center">Country</th>
                <th></th>
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
                    <InertiaLink v-if="can.edit" :href="route('universes.drivers.edit', [universe, driver])">
                        edit
                    </InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
    </template>
    <p v-else>No drivers found </p>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import { reactive, watch } from 'vue';
import { filter, sort } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon';

const props = defineProps({
    universe: Object,
    drivers: Object,
    filters: Object,
    can: Object,
});

const params = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

watch(params, () => {
    filter(params, route('universes.drivers.index', [ props.universe ]));
});
</script>

<script>
import Universe from '@/Layouts/Universe';

export default { layout: Universe };
</script>
