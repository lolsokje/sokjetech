<template>
	<BackLink :backTo="route('universes.series.show', [series.universe, series])" label="series overview"/>

	<h3>Engines</h3>

	<Link v-if="can.edit" :href="route('series.engines.create', [series])" class="btn btn-primary my-3">Add engine
	</Link>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>Name</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="engine in series.engines" :key="engine.id">
			<td>{{ engine.name }}</td>
			<td>
				<Link v-if="can.edit" :href="route('series.engines.edit', [series, engine])">edit</Link>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import BackLink from '../../Shared/BackLink';

defineProps({
	series: {
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
import Series from '../../Shared/Layouts/Series';

export default { layout: Series };
</script>
