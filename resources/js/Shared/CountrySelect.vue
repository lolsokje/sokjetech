<template>
	<div class="mb-3">
		<SearchableDropdown :items="countries" :label="label" :selected-item="selectedCountry" text-key="name"
							value-key="code"
							@selected="changeCountry"/>
	</div>
</template>

<script setup>
import countries from '../Utilities/Countries';
import { reactive } from 'vue';
import SearchableDropdown from './SearchableDropdown.vue';

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

const selectedCountry = countries.find((country) => country.code === props.country);

const state = reactive({
	country: props.country,
});

const emit = defineEmits(['countryChanged']);

function changeCountry (country) {
	state.country = country ? country.code : '';
	emit('countryChanged', state.country);
}
</script>
