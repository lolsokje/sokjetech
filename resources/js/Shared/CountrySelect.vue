<template>
	<div class="mb-3">
		<label class="form-label" for="country">{{ label }}</label>
		<select id="country" v-model="state.country" class="form-control" @change.prevent="changeCountry">
			<option value="">Select a country</option>
			<option v-for="(country, index) in countries" :key="index" :value="country.code">{{ country.name }}</option>
		</select>
	</div>
</template>

<script setup>
import countries from '../Utilities/Countries';
import { reactive } from 'vue';

const props = defineProps({
	label: {
		type: String,
		required: false,
		default: 'Country',
	},
	country: {
		type: String,
		required: false,
		default: '',
	},
});

const state = reactive({
	country: props.country,
});

const emit = defineEmits(['countryChanged']);

function changeCountry () {
	emit('countryChanged', state.country);
}
</script>
