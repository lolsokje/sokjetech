<template>
	<h1>Circuits</h1>

	<Link :href="route('circuits.create')" class="btn btn-primary my-3">Add circuit</Link>

	<template v-if="circuits.data.length">
		<input v-model="params.search" class="form-control mb-3 w-25" placeholder="Search" type="text">

		<table class="table table-borderless table-dark">
			<thead>
			<tr>
				<th role="button" @click="sort('name')">
					<span>Name</span>
					<OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
				</th>
				<th role="button" @click="sort('country')">
					<span>Country</span>
					<OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
				</th>
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
import { reactive, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import OrderIcon from '../../Shared/OrderIcon';

const props = defineProps({
	circuits: {
		type: Object,
		required: true,
	},
	filters: {
		type: Object,
		required: true,
	},
});

const params = reactive({
	search: props.filters.search ?? '',
	field: props.filters.field ?? '',
	direction: props.filters.direction ?? '',
});

const defaults = {
	search: '',
	field: 'name',
	direction: 'asc',
};

function sort (field) {
	params.field = field;
	params.direction = params.direction === 'desc' ? 'asc' : 'desc';
}

watch(params, () => {
	let requestParams = params;

	Object.entries(requestParams).forEach((entry) => {
		const [key, value] = entry;

		if (value === null || value === '') {
			delete requestParams[key];
		}
	});

	Inertia.get(route('circuits.index'), requestParams, { replace: true, preserveState: true });
});
</script>
