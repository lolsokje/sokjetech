<template>
	<BackLink :backTo="route('universes.index')" label="universe overview"/>

	<h3>Series</h3>

	<InertiaLink v-if="can.edit" :href="route('universes.series.create', [universe])" class="btn btn-primary my-3">
		Add series
	</InertiaLink>

	<table class="table table-bordered table-dark table-narrow">
		<thead>
		<tr>
			<th>Name</th>
			<th colspan="2"></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="series in universe.series" :key="series.id">
			<td>{{ series.name }}</td>
			<td class="small-centered">
				<InertiaLink v-if="can.edit" :href="route('universes.series.edit', [universe, series])">
					edit
				</InertiaLink>
			</td>
			<td class="small-centered">
				<InertiaLink :href="route('universes.series.show', [universe, series])">view</InertiaLink>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';

const props = defineProps({
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
import Universe from '@/Shared/Layouts/Universe';

export default { layout: Universe };
</script>
