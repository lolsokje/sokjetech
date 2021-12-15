<template>
	<h3>Series</h3>

	<Link v-if="can.edit" :href="route('universes.series.create', [universe])" class="btn btn-primary my-3">Add series
	</Link>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>Name</th>
			<th colspan="2"></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="series in universe.series" :key="series.id">
			<td>{{ series.name }}</td>
			<td>
				<Link :href="route('universes.series.edit', [universe, series])" v-if="can.edit">edit</Link>
			</td>
			<td>
				<Link :href="route('universes.series.show', [universe, series])">view</Link>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
	can: {
		type: Array,
		required: true,
	},
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
