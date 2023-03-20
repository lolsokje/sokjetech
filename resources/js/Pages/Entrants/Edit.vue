<template>
    <BackLink :backTo="route('seasons.entrants.index', [season])" label="team entries overview"/>

    <form class="form-narrow" @submit.prevent="form.put(route('seasons.entrants.update', [season, entrant]))">
        <SearchableDropdown :items="teams" :selected-item="selectedTeam" label="Select a base team" text-key="full_name"
                            value-key="id"
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
                <SearchableDropdown :items="engines" :selected-item="selectedEngine" label="Engine supplier"
                                    @selected="setEngineSupplier"
                />
            </div>

            <button class="btn btn-primary mt-3" type="submit">Save team</button>
        </template>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';
import TeamNamePreview from '@/Shared/TeamNamePreview.vue';
import ColourPicker from '@/Components/ColourPicker.vue';
import SeasonInterface from '@/Interfaces/Season';
import Team from '@/Interfaces/Team';
import { Engine } from '@/Interfaces/Engine';
import Entrant from '@/Interfaces/Entrant';
import Permission from '@/Interfaces/Permission';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    season: SeasonInterface,
    teams: Team[],
    engines: Engine[],
    entrant: Entrant,
    can: Permission,
}

const props = defineProps<Props>();

const form = useForm({
    team_id: props.entrant.team_id,
    full_name: props.entrant.full_name,
    short_name: props.entrant.short_name,
    team_principal: props.entrant.team_principal,
    primary_colour: props.entrant.primary_colour,
    secondary_colour: props.entrant.secondary_colour,
    accent_colour: props.entrant.accent_colour,
    country: props.entrant.country,
    engine_id: props.entrant.engine_id,
});

function setBaseTeam (team: Team): void {
    if (form.team_id === team.id) {
        return;
    }

    form.team_id = team.id;

    for (let key of Object.keys(form)) {
        if (team[key] !== undefined) {
            form[key] = team[key];
        }
    }

    form.country = team.country;
}

function setCountry (country: string): void {
    form.country = country;
}

function setEngineSupplier (engine: Engine): void {
    form.engine_id = engine.id;
}

const selectedTeam = computed(() => props.teams.find((team) => team.id === form.team_id));
const selectedEngine = computed(() => form.engine_id ? props.engines.find((engine) => engine.id === form.engine_id) : {});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
