<template>
	<h1>Circuits</h1>

	<Link :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</Link>

	<template v-if="circuits.data.length">
		<table class="table table-borderless table-dark">
			<thead>
			<tr>
				<th>Name</th>
				<th>Country</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="circuit in circuits.data" v-bind:key="circuit.id">
				<td>{{ circuit.name }}</td>
				<td>{{ circuit.country }}</td>
				<td>
					<Link :href="route('circuits.edit', circuit)">edit</Link>
				</td>
			</tr>
			</tbody>
		</table>
		<Pagination :links="circuits.links"/>
	</template>
	<p v-else>No circuits added yet</p>
</template>

<script setup>
import Pagination from '../../Shared/Pagination';

const props = defineProps({
	circuits: {
		type: Object,
		required: true,
	},
});
</script>
