<template>
	<BackLink :backTo="route('seasons.races.index', [season])" label="race overview"/>

	<form @submit.prevent="form.post(route('seasons.races.store', [season]))">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label class="form-label" for="name">Name</label>
			<input id="name" v-model="form.name" :placeholder="placeholder" class="form-control" type="text">
		</div>

		<div class="mb-3">
			<label class="form-label" for="circuit">Circuit</label>
			<select id="circuit" v-model="form.circuit_id" class="form-control" required>
				<option value="">Select a circuit</option>
				<option v-for="circuit in circuits" :key="circuit.id" :value="circuit.id">{{ circuit.name }}</option>
			</select>
		</div>

		<h4>Stints</h4>

		<button class="btn btn-primary my-3" @click.prevent="addStint">Add stint</button>

		<table class="table table-borderless table-dark">
			<thead>
			<tr class="text-center">
				<th>Stint</th>
				<th>Min RNG</th>
				<th>Max RNG</th>
				<th>DNF chance</th>
				<th>Use team rating</th>
				<th>Use driver rating</th>
				<th>Use engine rating</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="stint in form.stints" :key="stint.number" class="text-center">
				<td>{{ stint.number }}</td>
				<td><input v-model="stint.min_rng" class="form-control-sm" type="number"></td>
				<td><input v-model="stint.max_rng" class="form-control-sm" type="number"></td>
				<td><input v-model="stint.reliability" type="checkbox"></td>
				<td><input v-model="stint.use_team_rating" type="checkbox"></td>
				<td><input v-model="stint.use_driver_rating" type="checkbox"></td>
				<td><input v-model="stint.use_engine_rating" type="checkbox"></td>
				<td><span v-if="form.stints.length > 1" class="text-primary" role="button"
						  @click="deleteStint(stint.number)">delete stint</span></td>
			</tr>
			</tbody>
		</table>

		<button class="btn btn-primary" type="submit">Save race</button>
	</form>
</template>

<script setup>
import BackLink from '../../Shared/BackLink';
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	circuits: {
		type: Array,
		required: true,
	},
});

const placeholder = `${props.season.year} Example Grand Prix`;

const form = useForm({
	name: '',
	circuit_id: '',
	stints: [
		{
			number: 1,
			min_rng: 0,
			max_rng: 30,
			reliability: true,
			use_team_rating: true,
			use_driver_rating: true,
			use_engine_rating: true,
		},
	],
});

function addStint () {
	const lastNumber = form.stints[form.stints.length - 1].number;
	form.stints.push({
		number: lastNumber + 1,
		min_rng: 0,
		max_rng: 30,
		reliability: false,
		use_team_rating: false,
		use_driver_rating: false,
		use_engine_rating: false,
	});
}

function deleteStint (number) {
	form.stints = form.stints.filter((stint) => stint.number !== number);

	form.stints.forEach((stint, index) => {
		stint.number = index + 1;
	});
}
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
