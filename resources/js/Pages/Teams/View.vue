<template>

</template>

<script setup>
defineProps({
	universe: {
		type: Object,
		required: true,
	},
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
