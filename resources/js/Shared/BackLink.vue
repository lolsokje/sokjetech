<template>
	<Link :href="backTo" class="d-block my-3">&larr; back to {{ label }}</Link>
</template>

<script setup>
defineProps({
	backTo: {
		type: String,
		required: true,
	},
	label: {
		type: String,
		required: false,
		default: 'previous page',
	},
});
</script>
