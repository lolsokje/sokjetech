<template>
	<h2>Driver development</h2>

	<DriverDevelopment :form-route="route('seasons.development.drivers.store', [season])" :drivers="drivers"
					   :season="season"/>
</template>

<script setup>
import DriverDevelopment from '../../Shared/DriverDevelopment';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	drivers: {
		type: Array,
		required: true,
	},
});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
