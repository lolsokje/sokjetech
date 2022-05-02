<template>
	<BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

	<h2>Team development</h2>

	<TeamDevelopment :season="season" :teams="teams" :form-route="route('seasons.development.teams.store', [season])"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import TeamDevelopment from '@/Shared/TeamDevelopment';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	teams: {
		type: Array,
		required: true,
	},
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
