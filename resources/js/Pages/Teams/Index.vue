<template>
    <BackLink :backTo="route('universes.index')" label="universe overview"/>

    <h3>Teams</h3>

    <InertiaLink v-if="can.edit" :href="route('universes.teams.create', [universe])" class="btn btn-primary my-3">
        Add team
    </InertiaLink>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">
    <template v-if="teams.length">
        <table class="table">
            <thead>
            <tr>
                <th class="colour-accent"></th>
                <th role="button" @click.prevent="sort(params, 'full_name')">
                    Full name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="full_name"/>
                </th>
                <th role="button" @click.prevent="sort(params, 'short_name')">
                    Short name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="short_name"/>
                </th>
                <th role="button" @click.prevent="sort(params, 'team_principal')">
                    Team principal
                    <OrderIcon :current-field="params.field"
                               :direction="params.direction"
                               required-field="team_principal"
                    />
                </th>
                <th class="text-center">Country</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="team in teams" :key="team.id">
                <BackgroundColourCell :backgroundColour="team.primary_colour"/>
                <td class="padded-left">{{ team.full_name }}</td>
                <td class="padded-left">{{ team.short_name }}</td>
                <td class="padded-left">{{ team.team_principal }}</td>
                <td class="small-centered">
                    <CountryFlag :country="team.country"/>
                </td>
                <td class="small-centered">
                    <InertiaLink v-if="can.edit" :href="route('universes.teams.edit', [universe, team])">edit
                    </InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No teams found</p>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import BackgroundColourCell from '@/Components/BackgroundColourCell';
import { reactive, watch } from 'vue';
import { filter, sort } from '@/Composables/useTableFiltering';
import OrderIcon from '@/Shared/OrderIcon';
import Pagination from '@/Shared/Pagination';

const props = defineProps({
    universe: Object,
    teams: Array,
    links: Array,
    filters: Object,
    can: Object,
});

const params = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

watch(params, () => {
    filter(params, route('universes.teams.index', [ props.universe ]));
});
</script>

<script>
import Universe from '@/Layouts/Universe';

export default { layout: Universe };
</script>
