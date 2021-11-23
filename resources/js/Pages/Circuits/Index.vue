<template>
	<h1>Circuits</h1>

	<Link :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</Link>

	<table class="table table-dark" v-if="circuits.length">
		<thead>
		<tr>
			<th>Name</th>
			<th>Country</th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="circuit in circuits" v-bind:key="circuit.id">
			<td>{{ circuit.name }}</td>
			<td>{{ circuit.country }}</td>
		</tr>
		</tbody>
	</table>
	<p v-else>No circuits added yet</p>
</template>

<script>
export default {
	props: {
		circuits: {
			type: Array,
			required: true,
		},
	},
};
</script>
