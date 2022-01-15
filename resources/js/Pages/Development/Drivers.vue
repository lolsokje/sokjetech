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
		<button class="btn btn-primary" @click.prevent="applyDevRanges">Apply</button>

		<div class="col-4">
			<input id="edit-mode" v-model="state.hideInputs" class="form-check-inline mt-auto me-2"
				   type="checkbox">
			<label class="form-check-label" for="edit-mode">Hide inputs</label>
		</div>

		<div class="ms-auto">
			<button :disabled="!devCompleted" class="btn btn-success" @click.prevent="runDev">
				Run dev
			</button>
		</div>
	</div>

	<form @submit.prevent="store">
		<table id="screenshot-target" class="table table-bordered table-dark">
			<thead>
			<tr>
				<th class="text-center">#</th>
				<th>Driver</th>
				<th>Team</th>
				<th class="text-center">Current</th>
				<th v-if="inputsHidden" class="text-center">Min</th>
				<th v-if="inputsHidden" class="text-center">Max</th>
				<th class="text-center">Dev</th>
				<th class="text-center">New</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="driver in form.drivers" :key="driver.id">
				<td :style="driver.team_style" class="small-centered">{{ driver.number }}</td>
				<td>{{ driver.full_name }}</td>
				<td :style="driver.team_style">{{ driver.team_name }}</td>
				<td class="small-centered">{{ driver.rating }}</td>
				<td v-if="inputsHidden" class="big-centered px-3">
					<input v-model="driver.min" class="form-control" type="number">
				</td>
				<td v-if="inputsHidden" class="big-centered px-3">
					<input v-model="driver.max" class="form-control" type="number">
				</td>
				<td class="small-centered">{{ driver.dev }}</td>
				<td class="small-centered">{{ driver.new }}</td>
			</tr>
			</tbody>
		</table>

		<div class="d-flex">
			<CopyScreenshotButton/>
			<button :disabled="devCompleted" class="btn btn-primary ms-auto" type="submit">Save dev</button>
		</div>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { computed, onMounted, reactive } from 'vue';
import CopyScreenshotButton from '../../Shared/CopyScreenshotButton';
import Development from '../../Utilities/Development';

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

const form = useForm({
	drivers: [],
});

function applyDevRanges () {
	state.error = null;
	if (Development.validateDevRange(state)) {
		Development.applyDevRangesToItems(form.drivers, state);
	} else {
		state.error = 'The minimum bound must be equal to or lower than the maximum bound.';
	}
}

function runDev () {
	state.error = null;
	if (Development.performDev(form.drivers)) {
		state.devCompleted = true;
	} else {
		state.error = 'One of the drivers\' dev ranges are invalid, the minimum bound must be equal to or lower than the maximum bound.';
	}
}

function store () {
	Development.storeDev(form, route('seasons.development.drivers.store', [props.season]), state);
}

const devCompleted = computed(() => !state.devCompleted);
const inputsHidden = computed(() => !state.hideInputs);

onMounted(() => {
	props.drivers.forEach((driver) => {
		form.drivers.push({
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

	form.drivers.sort((a, b) => a.team_name.localeCompare(b.team_name));
});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
