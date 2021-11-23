<template>
	<h1>Circuits</h1>

	<Link :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</Link>

	<table class="table table-dark" v-if="circuits.length">
		<thead>
		<tr>
			<th>Name</th>
			<th>Country</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="circuit in circuits" v-bind:key="circuit.id">
			<td>{{ circuit.name }}</td>
			<td>{{ circuit.country }}</td>
			<td>
				<Link :href="route('circuits.edit', circuit)">edit</Link>
			</td>
		</tr>
		</tbody>
	</table>
	<p v-else>No circuits added yet</p>
</template>

<script setup>
const props = defineProps({
	circuits: {
		type: Array,
		required: true,
	},
});
</script>
