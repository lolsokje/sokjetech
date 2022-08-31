<template>
    <BackLink :backTo="route('universes.teams.index', [universe])" label="team overview"/>

    <form class="form-narrow" @submit.prevent="form.post(route('universes.teams.store', [universe]))">
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

        <CountrySelect @countryChanged="setCountry"></CountrySelect>

        <div class="row mb-3">
            <div class="col-3">
                <label class="form-label" for="primary_colour">Primary colour</label>
                <ColourPicker v-model="form.primary_colour" id="primary_colour" required/>
            </div>

            <div class="col-3">
                <label class="form-label" for="secondary_colour">Secondary colour</label>
                <ColourPicker v-model="form.secondary_colour" id="secondary_colour" required/>
            </div>

            <div class="col-3">
                <label class="form-label" for="accent_colour">Accent colour</label>
                <ColourPicker v-model="form.accent_colour" id="accent_colour" required/>
            </div>
        </div>

        <TeamNamePreview :team="form"/>

        <button class="btn btn-primary mt-3" type="submit">Save team</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import CountrySelect from '@/Shared/CountrySelect';
import Errors from '@/Shared/Errors';
import BackLink from '@/Shared/BackLink';
import TeamNamePreview from '@/Shared/TeamNamePreview';
import ColourPicker from '@/Components/ColourPicker';

defineProps({
    universe: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    full_name: '',
    short_name: '',
    team_principal: '',
    primary_colour: '#000000',
    secondary_colour: '#FFFFFF',
    accent_colour: '#000000',
    country: '',
});

function setCountry (country) {
    form.country = country;
}
</script>

<script>
import Universe from '@/Layouts/Universe';

export default { layout: Universe };
</script>
