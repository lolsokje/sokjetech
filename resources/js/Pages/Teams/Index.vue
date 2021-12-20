<template>
	<h3>Teams</h3>

	<Link v-if="can.edit" :href="route('universes.teams.create', [universe])" class="btn btn-primary my-3">Add team
	</Link>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>Full name</th>
			<th>Short name</th>
			<th>Team principal</th>
			<th>Country</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="team in universe.teams" :key="team.id">
			<td :style="team.styleString">{{ team.full_name }}</td>
			<td>{{ team.short_name }}</td>
			<td>{{ team.team_principal }}</td>
			<td>{{ team.country }}</td>
			<td>
				<Link :href="route('universes.teams.edit', [universe, team])" v-if="can.edit">edit</Link>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
defineProps({
	universe: {
		type: Object,
		required: true,
	},
	can: {
		type: Object,
		required: true,
	},
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
