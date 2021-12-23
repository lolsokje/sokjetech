<template>
	<template v-if="currentField === requiredField">
		<fa v-if="direction === 'asc'" icon="sort-alpha-up" class="ms-2"/>
		<fa v-if="direction === 'desc'" icon="sort-alpha-down-alt" class="ms-2"/>
	</template>
</template>

<script setup>
defineProps({
	currentField: {
		type: String,
		required: false,
	},
	requiredField: {
		type: String,
		required: false,
	},
	direction: {
		type: String,
		required: false,
	},
});
</script>

<script>
export default { name: 'OrderIcon' };
</script>
