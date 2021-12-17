<template>
	<h1>Create universe</h1>

	<form @submit.prevent="form.post(route('universes.store'))" class="form-narrow">
		<Errors :errors="form.errors"/>

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

		<button class="btn btn-primary">Create</button>
	</form>
</template>

<script setup>

import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';

const props = defineProps({
	visibilities: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: null,
	visibility: '',
});
</script>
