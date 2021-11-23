<template>
	<h1>Create circuit</h1>

	<form @submit.prevent="form.post(route('circuits.store'))">
		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" class="form-control" id="name" v-model="form.name" required>
		</div>

		<div class="mb-3">
			<label for="country" class="form-label">Country</label>
			<input type="text" class="form-control" id="country" v-model="form.country" required>
		</div>

		<button class="btn btn-primary">Create</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
	name: null,
	country: null,
});
</script>
