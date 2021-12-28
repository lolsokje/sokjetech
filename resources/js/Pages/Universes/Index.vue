<template>
	<h1>Universes</h1>

	<InertiaLink :href="route('universes.create')" class="btn btn-primary my-3">Add universe</InertiaLink>

	<table v-if="universes.length" class="table table-borderless table-dark">
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
				<InertiaLink v-if="universe.can.edit" :href="route('universes.edit', universe)">edit</InertiaLink>
			</td>
			<td>
				<InertiaLink :href="route('universes.show', universe)">view</InertiaLink>
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
