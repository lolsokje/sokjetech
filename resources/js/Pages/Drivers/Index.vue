<template>
	<h3>Drivers</h3>

	<Link :href="route('universes.drivers.create', [universe])" class="btn btn-primary my-3" v-if="can.edit">Add
		driver
	</Link>

	<table class="table table-borderless table-dark">
		<thead>
		<tr>
			<th>Driver name</th>
			<th>DOB</th>
			<th>Country</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="driver in universe.drivers" :key="driver.id">
			<td>{{ driver.fullName }}</td>
			<td>{{ driver.readableDob }}</td>
			<td>{{ driver.country }}</td>
			<td>
				<Link :href="route('universes.drivers.edit', [universe, driver])" v-if="can.edit">edit</Link>
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
		type: Array,
		required: true,
	},
});
</script>

<script>
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
