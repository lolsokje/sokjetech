<template>
	<BackLink :backTo="route('seasons.races.index', [season])" label="race overview"/>

	<form class="form-narrow" @submit.prevent="form.put(route('seasons.races.order', [season]))">
		<table class="table table-borderless table-dark">
			<thead>
			<tr>
				<th>#</th>
				<th>Race</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="race in form.races" :key="race.id" :data-race="race.id" draggable="true" role="button"
				@dragstart="dragStart($event, race.id)" @dragover.prevent="" @drop.prevent="drop">
				<td>{{ race.order }}</td>
				<td>{{ race.name }}</td>
			</tr>
			</tbody>
		</table>

		<button class="btn btn-primary" type="submit">Save order</button>
	</form>
</template>

<script setup>
import BackLink from '../../Shared/BackLink';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
});

const races = props.season.races;

const form = useForm({
	races: races.map((race) => {
		return {
			id: race.id,
			order: race.order,
			name: race.name,
		};
	}),
});

function dragStart (event, id) {
	event.dataTransfer.setData('id', id);
}

function drop (event) {
	const droppedRaceId = event.target.parentNode.dataset.race;
	const dropped = form.races.find((race) => race.id === droppedRaceId);
	const dragged = form.races.find((race) => race.id === event.dataTransfer.getData('id'));

	const droppedOrder = dropped.order;
	dropped.order = dragged.order;
	dragged.order = droppedOrder;

	form.races.sort((raceOne, raceTwo) => raceOne.order > raceTwo.order);
}
</script>

<script>
import Season from '../../Shared/Layouts/Season';

export default { layout: Season };
</script>
