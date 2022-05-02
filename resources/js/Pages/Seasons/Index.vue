<template>
	<BackLink :backTo="route('universes.series.show', [series.universe, series])" label="series overview"/>

	<h3>Seasons</h3>

	<InertiaLink v-if="can.edit" :href="route('series.seasons.create', [series])" class="btn btn-primary my-3">
		Create season
	</InertiaLink>

	<table class="table table-bordered table-dark table-narrow">
		<thead>
		<tr>
			<th>Year</th>
			<th colspan="2"></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="season in series.seasons" :key="season.id">
			<td>{{ season.year }}</td>
			<td class="small-centered">
				<InertiaLink v-if="can.edit" :href="route('series.seasons.edit', [series, season])">edit</InertiaLink>
			</td>
			<td class="small-centered">
				<InertiaLink :href="route('series.seasons.show', [series, season])">view</InertiaLink>
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
