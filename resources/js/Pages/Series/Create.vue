<template>
	<form @submit.prevent="form.post(route('universes.series.store', [universe]))" class="form-narrow">
		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" id="name" v-model="form.name" class="form-control" required>
		</div>

		<button type="submit" class="btn btn-primary">Save series</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

defineProps({
	universe: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: '',
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
