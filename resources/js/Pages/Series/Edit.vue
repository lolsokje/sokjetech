<template>
	<form @submit.prevent="form.put(route('universes.series.update', [universe, series]))" class="form-narrow">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" id="name" v-model="form.name" class="form-control" required>
		</div>

		<button type="submit" class="btn btn-primary">Save series</button>
	</form>
</template>

<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';

const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
	series: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: props.series.name,
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
