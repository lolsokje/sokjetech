<template>
    <BackLink :backTo="route('series.seasons.index', series)" label="series overview"/>

    <h3>Engines</h3>

    <template v-if="can.edit">
        <InertiaLink :href="route('series.engines.create', [series])" class="btn btn-primary my-3">
            Add engine
        </InertiaLink>

        <InertiaLink :href="route('database.engines.index')" class="text-decoration-underline ms-3">
            or copy an existing engine
        </InertiaLink>
    </template>

    <table class="table table-narrow">
        <thead>
        <tr>
            <th>Name</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="engine in series.engines" :key="engine.id">
            <td class="padded-left">{{ engine.name }}</td>
            <td class="small-centered">
                <InertiaLink v-if="can.edit" :href="route('series.engines.edit', [series, engine])">edit</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    series: Series,
    can: Permission,
}

defineProps<Props>();
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
