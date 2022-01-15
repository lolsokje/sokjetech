<template>
	<h2>Driver development</h2>

	<p v-if="state.error" class="text-danger">{{ state.error }}</p>
	<div class="row row-cols-lg-auto mb-3">
		<div class="col-4">
			<input id="drivers-min" v-model="state.min" class="form-control" type="number">
		</div>

		<div class="col-4">
			<input id="drivers-max" v-model="state.max" class="form-control" type="number">
		</div>
		<button class="btn btn-primary" @click.prevent="applyDriverDevRanges">Apply</button>

		<div class="col-4">
			<input id="edit-mode" v-model="state.hideInputs" class="form-check-inline mt-auto me-2"
				   type="checkbox">
			<label class="form-check-label" for="edit-mode">Hide inputs</label>
		</div>

		<div class="ms-auto">
			<button :disabled="!driverDevCompleted" class="btn btn-success" @click.prevent="runDriverDev">
				Run dev
			</button>
		</div>
	</div>

	<form
		@submit.prevent="storeDriverDev">
		<table class="table table-bordered table-dark">
			<thead>
			<tr>
				<th class="text-center">#</th>
				<th>Driver</th>
				<th>Team</th>
				<th class="text-center">Current</th>
				<th v-if="driverInputsHidden" class="text-center">Min</th>
				<th v-if="driverInputsHidden" class="text-center">Max</th>
				<th class="text-center">Dev</th>
				<th class="text-center">New</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="driver in driverDevelopmentForm.drivers" :key="driver.id">
				<td :style="driver.team_style" class="small-centered">{{ driver.number }}</td>
				<td>{{ driver.full_name }}</td>
				<td :style="driver.team_style">{{ driver.team_name }}</td>
				<td class="small-centered">{{ driver.rating }}</td>
				<td v-if="driverInputsHidden" class="big-centered px-3">
					<input v-model="driver.min" class="form-control" type="number">
				</td>
				<td v-if="driverInputsHidden" class="big-centered px-3">
					<input v-model="driver.max" class="form-control" type="number">
				</td>
				<td class="small-centered">{{ driver.dev }}</td>
				<td class="small-centered">{{ driver.new }}</td>
			</tr>
			</tbody>
		</table>

		<div class="d-flex">
			<button :disabled="driverDevCompleted" class="btn btn-primary ms-auto" type="submit">Save dev</button>
		</div>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { computed, onMounted, reactive } from 'vue';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	drivers: {
		type: Array,
		required: true,
	},
});

const state = reactive({
	error: null,
	devCompleted: false,
	hideInputs: false,
	min: 0,
	max: 0,
});

const driverDevelopmentForm = useForm({
	drivers: [],
});

function applyDriverDevRanges () {
	const min = state.min;
	const max = state.max;

	if (min >= max) {
		state.error = 'The minimum bound must be lower than the maximum bound';
		return;
	} else {
		state.error = null;
	}

	driverDevelopmentForm.drivers.forEach((driver) => {
		driver.min = Math.round(min);
		driver.max = Math.round(max);
	});
}

function runDriverDev () {
	verifyDevRanges();

	if (state.error !== null) {
		return;
	}

	driverDevelopmentForm.drivers.forEach((driver) => {
		const min = driver.min;
		const max = driver.max;

		const dev = Math.round(Math.random() * (max - min) + min);
		driver.dev = dev;
		driver.new = driver.rating + dev;
	});

	state.devCompleted = true;
}

function verifyDevRanges () {
	state.error = null;

	driverDevelopmentForm.drivers.forEach((driver) => {
		if (driver.min >= driver.max) {
			state.error = `The minimum bound for ${driver.full_name} must be lower than the maximum bound`;
		}
	});
}

function storeDriverDev () {
	driverDevelopmentForm.post(route('seasons.development.drivers.store', [props.season]), {
		preserveState: true,
		onSuccess: () => state.devCompleted = false,
	});
}

function resetDriverDev () {
	state.min = 0;
	state.max = 0;

	driverDevelopmentForm.drivers.forEach((driver) => {
		driver.min = 0;
		driver.max = 0;
		driver.dev = 0;
	});
}

const driverDevCompleted = computed(() => !state.devCompleted);
const driverInputsHidden = computed(() => !state.hideInputs);

onMounted(() => {
	props.drivers.forEach((driver) => {
		driverDevelopmentForm.drivers.push({
			id: driver.id,
			number: driver.number,
			team_name: driver.entrant.full_name,
			team_style: driver.entrant.style_string,
			full_name: driver.driver.full_name,
			rating: driver.rating,
			min: 0,
			max: 0,
			dev: 0,
			new: driver.rating,
		});
	});

	driverDevelopmentForm.drivers.sort((a, b) => a.team_name.localeCompare(b.team_name));
});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
