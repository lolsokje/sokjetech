<template>
	<h3>Entrants</h3>

	<InertiaLink v-if="can.edit" :href="route('seasons.entrants.create', [season])" class="btn btn-primary mb-3">
		Add entrant
	</InertiaLink>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>Full name</th>
			<th>Short name</th>
			<th>Team principal</th>
			<th>Engine supplier</th>
			<th>Country</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="entrant in season.entrants" :key="entrant.id">
			<td :style="entrant.style_string">{{ entrant.full_name }}</td>
			<td>{{ entrant.short_name }}</td>
			<td>{{ entrant.team_principal }}</td>
			<td>
				<span v-if="entrant.engine">
					{{ entrant.engine.name }}
				</span>
			</td>
			<td>{{ entrant.country }}</td>
			<td>
				<InertiaLink :href="route('seasons.entrants.edit', [season, entrant])">edit</InertiaLink>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
defineProps({
	season: {
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
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>