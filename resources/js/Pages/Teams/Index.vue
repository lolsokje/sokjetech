<template>
    <Breadcrumb :link="route('universes.index')" linkText="Universes" :label="universe.name" append="Teams"/>

    <template v-if="can.edit">
        <InertiaLink :href="route('universes.teams.create', [universe])" class="btn btn-primary my-3">
            Add team
        </InertiaLink>

        <InertiaLink :href="route('database.teams.index')" class="text-decoration-underline ms-3">
            or copy an existing team
        </InertiaLink>
    </template>

    <input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">
    <template v-if="teams.length">
        <table class="table">
            <thead>
            <tr>
                <th class="colour-accent"></th>
                <th role="button" @click.prevent="sortTable(params, 'full_name')">
                    Full name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="full_name"/>
                </th>
                <th role="button" @click.prevent="sortTable(params, 'short_name')">
                    Short name
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="short_name"/>
                </th>
                <th role="button" @click.prevent="sortTable(params, 'team_principal')">
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
                <BackgroundColourCell :backgroundColour="team.accent_colour"/>
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

<script setup lang="ts">
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { reactive, watch } from 'vue';
import { filter, sortTable } from '@/Composables/useTableFiltering.js';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import PaginationLink from '@/Interfaces/PaginationLink';
import Filters from '@/Interfaces/Filters';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    universe: Universe,
    teams: Array<Team>,
    links: Array<PaginationLink>,
    filters: Filters,
    can: Permission,
}

const props = defineProps<Props>();

const params: Filters = reactive({
    search: props.filters.search ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
});

watch(params, () => {
    filter(params, route('universes.teams.index', [ props.universe ]));
});
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
