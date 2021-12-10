<template>
	<h1>Universes</h1>

	<Link :href="route('universes.create')" class="btn btn-primary my-3">Add universe</Link>

	<table class="table table-borderless table-dark" v-if="universes.length">
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
			<td>
				<Link :href="route('universes.edit', universe)" v-if="universe.can.edit">edit</Link>
			</td>
			<td>
				<Link :href="route('universes.show', universe)">view</Link>
			</td>
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
