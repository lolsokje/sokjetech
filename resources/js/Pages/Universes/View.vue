<template>
	<h1>{{ universe.name }}</h1>
</template>

<script setup>
const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
});
</script>
