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
		required: true,
	},
	requiredField: {
		type: String,
		required: true,
	},
	direction: {
		type: String,
		required: true,
	},
});
</script>

<script>
export default { name: 'OrderIcon' };
</script>
