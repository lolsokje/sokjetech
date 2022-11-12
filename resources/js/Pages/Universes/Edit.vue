<template>
	<h1>Update {{ universe.name }}</h1>

	<BackLink :backTo="route('universes.index')" label="universe overview"/>

	<form class="form-narrow" @submit.prevent="form.put(route('universes.update', universe))">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label class="form-label" for="name">Name</label>
			<input id="name" v-model="form.name" class="form-control" required type="text">
		</div>

		<div class="mb-3">
			<label class="form-label" for="visibility">Visibility</label>
			<select id="visibility" v-model="form.visibility" class="form-select" required>
				<option value="">Select an option</option>
				<option v-for="(visibility, id) in visibilities" :key="id" :value="id">{{ visibility }}</option>
			</select>
		</div>

		<button class="btn btn-primary">Update</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors.vue';
import BackLink from '@/Shared/BackLink.vue';

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
