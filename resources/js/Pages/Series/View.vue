<template>
	<BackLink :backTo="route('universes.series.index', [universe])" label="series overview"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';

defineProps({
	universe: {
		type: Object,
		required: true,
	},
});
</script>

<script>
import Series from '@/Shared/Layouts/Series';

export default { layout: Series };
</script>
