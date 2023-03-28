<template>
    <Breadcrumb :link="route('universes.index')" linkText="Universes" :label="universe.name" append="Series"/>

    <InertiaLink v-if="can.edit" :href="route('universes.series.create', [universe])" class="btn btn-primary my-3">
        Add series
    </InertiaLink>

    <table class="table table-narrow">
        <thead>
        <tr>
            <th>Name</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="series in universe.series" :key="series.id">
            <td class="padded-left">{{ series.name }}</td>
            <td class="small-centered">
                <InertiaLink v-if="can.edit" :href="route('universes.series.edit', [universe, series])">
                    edit
                </InertiaLink>
            </td>
            <td class="small-centered">
                <InertiaLink :href="route('series.seasons.index', series)">view</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup lang="ts">

import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    universe: Universe,
    can: Permission
}

const props = defineProps<Props>();
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
