<template>
	<h1>Create circuit</h1>

	<form class="form-narrow" @submit.prevent="form.post(route('circuits.store'))">
		<Errors :errors="form.errors"/>

		<div class="mb-3">
			<label class="form-label" for="name">Name</label>
			<input id="name" v-model="form.name" class="form-control" required type="text">
		</div>

		<CountrySelect @countryChanged="setCountry"/>

		<button class="btn btn-primary">Create</button>
	</form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors';
import CountrySelect from '@/Shared/CountrySelect';

const form = useForm({
	name: null,
	country: null,
});

function setCountry (country) {
	form.country = country;
}
</script>
