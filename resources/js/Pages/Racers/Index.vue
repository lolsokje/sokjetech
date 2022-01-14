<template>
	<h3>Drivers</h3>

	<table class="table table-bordered table-dark">
		<thead>
		<tr class="text-center">
			<th>Team name</th>
			<th>Driver</th>
			<th>#</th>
			<th>Team</th>
			<th>Driver</th>
			<th>Engine</th>
			<th>Total</th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="driver in drivers" :key="driver.id">
			<td :style="driver.style_string">{{ driver.team_name }}</td>
			<td>{{ driver.driver_name }}</td>
			<td :style="driver.style_string" class="small-centered">{{ driver.number }}</td>
			<td class="small-centered">{{ driver.team_rating }}</td>
			<td class="small-centered">{{ driver.driver_rating }}</td>
			<td class="small-centered">{{ driver.engine_rating }}</td>
			<td class="small-centered">{{ driver.total_rating }}</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	racers: {
		type: Array,
		required: true,
	},
});

const drivers = ref([]);

onMounted(() => {
	props.racers.forEach((racer) => {
		const entrant = racer.entrant;
		const engineRating = 10;
		const totalRating = racer.rating + entrant.rating + engineRating;

		drivers.value.push({
			style_string: entrant.style_string,
			team_name: entrant.full_name,
			team_rating: entrant.rating,
			driver_name: racer.driver.full_name,
			number: racer.number,
			driver_rating: racer.rating,
			engine_rating: engineRating,
			total_rating: totalRating,
		});
	});

	drivers.value.sort((a, b) => {
		return a.total_rating < b.total_rating;
	});
});
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
