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
import Series from '../../Shared/Layouts/Series';

export default { layout: Series };
</script>
