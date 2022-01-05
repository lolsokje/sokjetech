<template>
	<form class="form-narrow" @submit.prevent="form.put(route('seasons.entrants.update', [season, entrant]))">
		<SearchableDropdown :items="teams" :selected-item="selectedTeam" label="Select a base team" text-key="full_name"
							value-key="id"
							@selected="setBaseTeam"/>

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
					<input id="primary_colour" v-model="form.primary_colour" class="form-control w-50 h-75" required
						   type="color">
				</div>

				<div class="col-3">
					<label class="form-label" for="secondary_colour">Secondary colour</label>
					<input id="secondary_colour" v-model="form.secondary_colour" class="form-control w-50 h-75" required
						   type="color">
				</div>
			</div>

			<TeamNamePreview :background-colour="form.primary_colour" :name="form.full_name"
							 :text-colour="form.secondary_colour"/>

			<div v-if="engines.length" class="my-3">
				<SearchableDropdown :items="engines" :selected-item="selectedEngine" label="Engine supplier"
									@selected="setEngineSupplier"/>
			</div>

			<button class="btn btn-primary mt-3" type="submit">Save team</button>
		</template>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import SearchableDropdown from '../../Shared/SearchableDropdown';
import CountrySelect from '../../Shared/CountrySelect';
import TeamNamePreview from '../../Shared/TeamNamePreview';
import { computed } from 'vue';

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
	entrant: {
		type: Object,
		required: true,
	},
	can: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	team_id: props.entrant.team_id,
	full_name: props.entrant.full_name,
	short_name: props.entrant.short_name,
	team_principal: props.entrant.team_principal,
	primary_colour: props.entrant.primary_colour,
	secondary_colour: props.entrant.secondary_colour,
	country: props.entrant.country,
	engine_id: props.entrant.engine_id,
});

function setBaseTeam (team) {
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

function setCountry (country) {
	form.country = country;
}

function setEngineSupplier (engine) {
	form.engine_id = engine.id;
}

const selectedTeam = computed(() => props.teams.find((team) => team.id === form.team_id));
const selectedEngine = computed(() => form.engine_id ? props.engines.find((engine) => engine.id === form.engine_id) : {});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
