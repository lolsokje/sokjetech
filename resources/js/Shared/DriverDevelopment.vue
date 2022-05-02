<template>
	<template v-if="drivers.length">
		<p v-if="state.error" class="text-danger">{{ state.error }}</p>
		<div class="row row-cols-lg-auto mb-3">
			<div class="col-4">
				<input id="drivers-min" v-model="state.min" class="form-control" type="number">
			</div>

			<div class="col-4">
				<input id="drivers-max" v-model="state.max" class="form-control" type="number">
			</div>
			<button class="btn btn-primary" @click.prevent="applyDevRanges">Apply</button>

			<div class="ms-auto">
				<button :disabled="!devCompleted" class="btn btn-success" @click.prevent="runDev">
					Run dev
				</button>
			</div>
		</div>

		<div class="mb-3">
			<div>
				<div class="form-check-inline">
					<input id="edit-mode" v-model="state.hideInputs" class="form-check-inline" type="checkbox">
					<label class="form-check-label" for="edit-mode">Hide inputs</label>
				</div>
			</div>

			<div>
				<div class="form-check-inline">
					<input type="checkbox" id="edit-ratings" v-model="state.editRatings" class="form-check-inline">
					<label for="edit-ratings" class="form-check-label">Edit ratings directly?</label>
				</div>
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
					<td class="small-centered">
						<template v-if="!state.editRatings">{{ driver.new }}</template>
						<template v-else>
							<input type="number" class="form-control" v-model="driver.new">
						</template>
					</td>
				</tr>
				</tbody>
			</table>

			<div class="d-flex">
				<CopyScreenshotButton/>
				<button :disabled="devCompleted && !state.editRatings" class="btn btn-primary ms-auto" type="submit">
					Save dev
				</button>
			</div>
		</form>
	</template>
	<p v-else>No drivers have been added to any entrants yet</p>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Development from '../Utilities/Development';
import DevelopmentDriver from '../Utilities/DevelopmentDriver';
import CopyScreenshotButton from './CopyScreenshotButton';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	drivers: {
		type: Array,
		required: true,
	},
	type: {
		type: String,
		required: false,
		default: 'development',
	},
	formRoute: {
		type: String,
		required: true,
	},
});

const state = reactive({
	error: null,
	completed: false,
	hideInputs: false,
	min: 0,
	max: 0,
	editRatings: false,
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
	Development.storeDev(form, props.formRoute, state);
}

const devCompleted = computed(() => !state.devCompleted);
const inputsHidden = computed(() => !state.hideInputs);

onMounted(() => {
	props.drivers.forEach((driver) => {
		form.drivers.push(new DevelopmentDriver(driver, props.type === 'reliability'));
	});

	form.drivers.sort((a, b) => a.team_name.localeCompare(b.team_name));
});
</script>

<script>
export default { name: 'DriverDevelopment' };
</script>
