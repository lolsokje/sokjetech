<template>
	<form @submit.prevent="form.post(route('series.engines.store', [series]))" class="form-narrow">
		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" id="name" v-model="form.name" class="form-control" required>
		</div>

		<button type="submit" class="btn btn-primary">Save engine</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

defineProps({
	series: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: '',
});
</script>

<script>
import Series from '../../Shared/Layouts/Series';

export default { layout: Series };
</script>
