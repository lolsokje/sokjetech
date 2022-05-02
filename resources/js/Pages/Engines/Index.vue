<template>
	<BackLink :backTo="route('universes.series.show', [series.universe, series])" label="series overview"/>

	<h3>Engines</h3>

	<InertiaLink v-if="can.edit" :href="route('series.engines.create', [series])" class="btn btn-primary my-3">
		Add engine
	</InertiaLink>

	<table class="table table-bordered table-dark table-narrow">
		<thead>
		<tr>
			<th>Name</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="engine in series.engines" :key="engine.id">
			<td>{{ engine.name }}</td>
			<td class="small-centered">
				<InertiaLink v-if="can.edit" :href="route('series.engines.edit', [series, engine])">edit</InertiaLink>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';

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
import Series from '@/Shared/Layouts/Series';

export default { layout: Series };
</script>
