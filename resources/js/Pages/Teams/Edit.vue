<template>
    <Breadcrumb :link="route('universes.teams.index', universe)"
                :linkText="universe.name"
                :label="team.full_name"
                append="Edit team"
    />

    <form class="form-narrow" @submit.prevent="form.put(route('universes.teams.update', [universe, team]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="full_name">Full name</label>
            <input id="full_name" v-model="form.full_name" class="form-control" required type="text">
        </div>

        <div class="mb-3">
            <label class="form-label" for="short_name">Short name</label>
            <input id="short_name" v-model="form.short_name" class="form-control" required type="text">
        </div>

        <div class="mb-3">
            <label class="form-label" for="team_principal">Team principal</label>
            <input id="team_principal" v-model="form.team_principal" class="form-control" required type="text">
        </div>

        <CountrySelect :country="form.country" @countryChanged="setCountry"></CountrySelect>

        <div class="row mb-3">
            <div class="col-lg-4 col-md-6 col-12">
                <label class="form-label" for="primary_colour">Primary colour</label>
                <ColourPicker v-model="form.primary_colour" id="primary_colour" required/>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <label class="form-label" for="secondary_colour">Secondary colour</label>
                <ColourPicker v-model="form.secondary_colour" id="secondary_colour" required/>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <label class="form-label" for="accent_colour">Accent colour</label>
                <ColourPicker v-model="form.accent_colour" id="accent_colour" required/>
            </div>
        </div>

        <TeamNamePreview :team="form"/>

        <button class="btn btn-primary mt-3" type="submit">Save team</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import CountrySelect from '@/Shared/CountrySelect.vue';
import Errors from '@/Shared/Errors.vue';
import TeamNamePreview from '@/Shared/TeamNamePreview.vue';
import ColourPicker from '@/Components/ColourPicker.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    universe: Universe,
    team: Team,
}

const props = defineProps<Props>();

const form = useForm({
    full_name: props.team.full_name,
    short_name: props.team.short_name,
    team_principal: props.team.team_principal,
    primary_colour: props.team.primary_colour,
    secondary_colour: props.team.secondary_colour,
    accent_colour: props.team.accent_colour,
    country: props.team.country,
});

const setCountry = (country: string): void => {
    form.country = country;
};
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
