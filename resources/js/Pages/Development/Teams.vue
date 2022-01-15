<template>
	<h2>Team development</h2>

	<p v-if="state.error" class="text-danger">{{ state.error }}</p>
	<div class="row row-cols-lg-auto mb-3">
		<div class="col-4">
			<input id="teams-min" v-model="state.min" class="form-control" type="number">
		</div>

		<div class="col-4">
			<input id="teams-max" v-model="state.max" class="form-control" type="number">
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

	<form @submit.prevent="storeDev">
		<table id="screenshot-target" class="table table-bordered table-dark">
			<thead>
			<tr>
				<th>Name</th>
				<th class="text-center">Current</th>
				<th v-if="inputsHidden" class="text-center">Min</th>
				<th v-if="inputsHidden" class="text-center">Max</th>
				<th class="text-center">Dev</th>
				<th class="text-center">New</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="team in form.teams" :key="team.id">
				<td :style="team.style">{{ team.name }}</td>
				<td class="small-centered">{{ team.rating }}</td>
				<td v-if="inputsHidden" class="big-centered px-3">
					<input v-model="team.min" class="form-control" type="number">
				</td>
				<td v-if="inputsHidden" class="big-centered px-3">
					<input v-model="team.max" class="form-control" type="number">
				</td>
				<td class="small-centered">{{ team.dev }}</td>
				<td class="small-centered">{{ team.new }}</td>
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
import { performDev } from '../../Utilities/Development';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	teams: {
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
	teams: [],
});

function applyDevRanges () {
	const min = state.min;
	const max = state.max;

	if (min >= max) {
		state.error = 'The minimum bound must be lower than the maximum bound';
		return;
	} else {
		state.error = null;
	}

	form.teams.forEach((team) => {
		team.min = Math.round(min);
		team.max = Math.round(max);
	});
}

function runDev () {
	verifyDevRanges();

	if (state.error !== null) {
		return;
	}

	form.teams.forEach((team) => {
		performDev(team);
	});

	state.devCompleted = true;
}

function verifyDevRanges () {
	state.error = null;

	form.teams.forEach((team) => {
		if (team.min >= team.max) {
			state.error = `The minimum bound for ${team.full_name} must be lower than the maximum bound`;
		}
	});
}

function storeDev () {
	form.post(route('seasons.development.teams.store', [props.season]), {
		preserveState: true,
		onSuccess: () => state.devCompleted = false,
	});
}

const devCompleted = computed(() => !state.devCompleted);
const inputsHidden = computed(() => !state.hideInputs);

onMounted(() => {
	props.teams.forEach((team) => {
		form.teams.push({
			id: team.id,
			name: team.team.full_name,
			style: team.style_string,
			rating: team.rating,
			min: 0,
			max: 0,
			dev: 0,
			new: team.rating,
		});
	});
});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
