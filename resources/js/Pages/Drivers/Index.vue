<template>
	<BackLink :backTo="route('universes.index')" label="universe overview"/>

	<h3>Drivers</h3>

	<InertiaLink v-if="can.edit" :href="route('universes.drivers.create', [universe])" class="btn btn-primary my-3">
		Add driver
	</InertiaLink>

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
				<InertiaLink v-if="can.edit" :href="route('universes.drivers.edit', [universe, driver])">
					edit
				</InertiaLink>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
import BackLink from '../../Shared/BackLink';

defineProps({
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
import Universe from '../../Shared/Layouts/Universe';

export default { layout: Universe };
</script>
