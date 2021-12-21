<template>
	<BackLink :backTo="route('universes.teams.index', [universe])" label="team overview"/>

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

		<button class="btn btn-primary mt-3" type="submit">Save team</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import CountrySelect from '../../Shared/CountrySelect';
import Errors from '../../Shared/Errors';
import BackLink from '../../Shared/BackLink';

const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
	team: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	full_name: props.team.full_name,
	short_name: props.team.short_name,
	team_principal: props.team.team_principal,
	primary_colour: props.team.primary_colour,
	secondary_colour: props.team.secondary_colour,
	country: props.team.country,
});

function setCountry (country) {
	form.country = country;
}
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
