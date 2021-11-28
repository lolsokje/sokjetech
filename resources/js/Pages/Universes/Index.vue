<template>
	<h1>Universes</h1>

	<table class="table table-dark" v-if="universes.length">
		<thead>
		<tr>
			<th>Name</th>
			<th></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="universe in universes" v-bind:key="universe.id">
			<td>{{ universe.name }}</td>
			<td>edit</td>
			<td>view</td>
		</tr>
		</tbody>
	</table>
	<p v-else>No universes added yet</p>
</template>

<script setup>
const props = defineProps({
	universes: {
		type: Array,
		required: true,
	},
});
</script>
