<template>
	<h1>Update {{ universe.name }}</h1>

	<form @submit.prevent="form.put(route('universes.update', universe))" class="form-narrow">
		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" class="form-control" id="name" v-model="form.name" required>
		</div>

		<div class="mb-3">
			<label for="visibility" class="form-label">Visibility</label>
			<select v-model="form.visibility" id="visibility" class="form-select" required>
				<option value="">Select an option</option>
				<option v-for="(visibility, id) in visibilities" :value="id" :key="id">{{ visibility }}</option>
			</select>
		</div>

		<button class="btn btn-primary">Update</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
	visibilities: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: props.universe.name,
	visibility: props.universe.visibility,
});
</script>
