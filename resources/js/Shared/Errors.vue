<template>
	<p v-for="(error, index) in errors" :key="index" class="text-danger">{{ error }}</p>
</template>

<script setup>
defineProps({
	errors: {
		type: Object,
		required: true,
	},
});
</script>
