<template>
	<form @submit.prevent="form.post(route('series.seasons.store', [series]))" class="form-narrow">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label for="year" class="form-label">Year</label>
			<input type="number" id="year" v-model="form.year" class="form-control" min="0" required>
		</div>

		<button type="submit" class="btn btn-primary">Save season</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';

defineProps({
	series: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	year: '',
});
</script>

<script>
import Series from '../../Shared/Layouts/Series';

export default { layout: Series };
</script>
