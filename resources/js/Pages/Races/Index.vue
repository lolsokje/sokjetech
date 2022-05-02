<template>
	<BackLink :backTo="route('series.seasons.index', [season.series])" label="series overview"/>

	<h3>Races</h3>

	<InertiaLink v-if="can.edit" :href="route('seasons.races.create', [season])" class="btn btn-primary my-3">
		Add race
	</InertiaLink>
	<InertiaLink v-if="can.edit && season.races.length > 1" :href="route('seasons.races.reorder', [season])"
				 class="btn btn-primary my-3 ms-3">
		Reorder races
	</InertiaLink>

	<table class="table table-bordered table-dark">
		<thead>
		<tr>
			<th class="text-center">#</th>
			<th>Name</th>
			<th class="text-center">Completed</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="race in season.races" :key="race.id">
			<td class="small-centered">{{ race.order }}</td>
			<td>{{ race.name }}</td>
			<td class="big-centered">
				<fa v-if="race.completed" icon="check"/>
				<fa v-else icon="times"/>
			</td>
			<td class="small-centered">
				<InertiaLink v-if="can.edit && !race.completed" :href="route('seasons.races.edit', [season, race])">
					edit
				</InertiaLink>
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
