<template>
	<BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

	<h3>Races</h3>

	<Link v-if="can.edit" :href="route('seasons.races.create', [season])" class="btn btn-primary my-3">Add race</Link>
	<Link v-if="can.edit && season.races.length > 1" :href="route('seasons.races.reorder', [season])"
		  class="btn btn-primary my-3 ms-3">Reorder
		races
	</Link>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Completed</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="race in season.races" :key="race.id">
			<td>{{ race.order }}</td>
			<td>{{ race.name }}</td>
			<td>
				<fa v-if="race.completed" icon="check"/>
				<fa v-else icon="times"/>
			</td>
			<td>
				<Link v-if="can.edit && !race.completed" :href="route('seasons.races.edit', [season, race])">edit</Link>
				<a v-if="race.completed" href="#">results</a>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import BackLink from '../../Shared/BackLink';

const props = defineProps({
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
