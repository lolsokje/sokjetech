<template>
	<h2>Team reliability</h2>

	<TeamDevelopment :season="season" :teams="teams" type="reliability"
					 :form-route="route('seasons.development.reliability.teams.store', [season])"/>
</template>

<script setup>
import TeamDevelopment from '../../../Shared/TeamDevelopment';

defineProps({
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
import Season from '../../../Shared/Layouts/Season';

export default { layout: Season };
</script>
