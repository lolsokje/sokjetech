<template>
	<BackLink :backTo="route('series.seasons.index', [season.series])" label="season overview"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';

defineProps({
	season: {
		type: Object,
		required: true,
	},
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
