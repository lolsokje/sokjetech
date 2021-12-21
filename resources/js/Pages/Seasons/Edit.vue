<template>
	<form @submit.prevent="form.put(route('series.seasons.update', [series, season]))" class="form-narrow">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label for="year" class="form-label">Year</label>
			<input type="number" id="year" v-model="form.year" min="0" class="form-control" required>
		</div>

		<button type="submit" class="btn btn-primary">Save season</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';

const props = defineProps({
	series: {
		type: Object,
		required: true,
	},
	season: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	year: props.season.year,
});
</script>

<script>
import Series from '../../Shared/Layouts/Series';

export default { layout: Series };
</script>
