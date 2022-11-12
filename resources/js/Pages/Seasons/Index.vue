<template>
    <BackLink :backTo="route('universes.series.index', [series.universe])" label="series overview"/>

    <h3>Seasons</h3>

    <InertiaLink v-if="can.edit" :href="route('series.seasons.create', [series])" class="btn btn-primary my-3">
        Create season
    </InertiaLink>

    <table class="table table-narrow">
        <thead>
        <tr>
            <th>Season</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="season in series.seasons" :key="season.id">
            <td class="padded-left">{{ season.full_name }}</td>
            <td class="small-centered">
                <InertiaLink v-if="can.edit" :href="route('series.seasons.edit', [series, season])">edit</InertiaLink>
            </td>
            <td class="small-centered">
                <InertiaLink :href="route('series.seasons.show', [series, season])">view</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';

defineProps({
    series: {
        type: Object,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
