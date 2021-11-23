<template>
	<h1>Edit "{{ circuit.name }}"</h1>

	<form @submit.prevent="form.put(route('circuits.update', circuit))">
		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" class="form-control" id="name" v-model="form.name" required>
		</div>

		<div class="mb-3">
			<label for="country" class="form-label">Country</label>
			<input type="text" class="form-control" id="country" v-model="form.country" required>
		</div>

		<button class="btn btn-primary">Update</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
	circuit: { type: Object, required: true },
});

const form = useForm({
	name: props.circuit.name,
	country: props.circuit.country,
});
</script>
