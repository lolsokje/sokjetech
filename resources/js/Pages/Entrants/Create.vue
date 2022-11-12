<template>
    <form class="form-narrow" @submit.prevent="form.post(route('seasons.entrants.store', [season]))">
        <SearchableDropdown :items="teams" label="Select a base team" text-key="full_name" value-key="id"
                            @selected="setBaseTeam"
        />

        <template v-if="form.team_id !== ''">
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

            <div v-if="engines.length" class="my-3">
                <SearchableDropdown :items="engines" label="Engine supplier" @selected="setEngineSupplier"/>
            </div>

            <button class="btn btn-primary mt-3" type="submit">Save team</button>
        </template>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';
import TeamNamePreview from '@/Shared/TeamNamePreview.vue';
import ColourPicker from '@/Components/ColourPicker.vue';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    teams: {
        type: Array,
        required: true,
    },
    engines: {
        type: Array,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    team_id: '',
    full_name: '',
    short_name: '',
    team_principal: '',
    primary_colour: '',
    secondary_colour: '',
    accent_colour: '',
    country: '',
    engine_id: null,
});

function setBaseTeam (team) {
    form.team_id = team.id;

    for (let key of Object.keys(form)) {
        if (team[key] !== undefined) {
            form[key] = team[key];
        }
    }

    form.country = team.country;
}

function setCountry (country) {
    form.country = country;
}

function setEngineSupplier (engine) {
    form.engine_id = engine.id;
}
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
