<template>
	<h1>Edit "{{ circuit.name }}"</h1>

	<form class="form-narrow" @submit.prevent="form.put(route('circuits.update', circuit))">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label class="form-label" for="name">Name</label>
			<input id="name" v-model="form.name" class="form-control" required type="text">
		</div>

		<CountrySelect :country="form.country" @countryChanged="setCountry"/>

		<button class="btn btn-primary">Update</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '../../Shared/Errors';
import CountrySelect from '../../Shared/CountrySelect';

const props = defineProps({
	circuit: { type: Object, required: true },
});

const form = useForm({
	name: props.circuit.name,
	country: props.circuit.country,
});

function setCountry (country) {
	form.country = country;
}
</script>
