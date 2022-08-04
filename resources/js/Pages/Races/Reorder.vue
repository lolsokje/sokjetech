<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="race overview"/>

    <form class="form-narrow" @submit.prevent="form.put(route('seasons.races.order', [season]))">
        <p>
            <fa icon="info-circle"/>
            <i class="ms-2">drag and drop to reorder races</i>
        </p>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Race</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="race in form.races" :key="race.id" :data-race="race.id" draggable="true" role="button"
                @dragstart="dragStart($event, race.id)" @dragover.prevent="" @drop.prevent="drop">
                <td class="small-centered">{{ race.order }}</td>
                <td class="padded-left">{{ race.name }}</td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" type="submit">Save order</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import BackLink from '@/Shared/BackLink';

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
    const droppedOnRaceId = event.target.parentNode.dataset.race;
    const droppedOn = form.races.find((race) => parseInt(race.id) === parseInt(droppedOnRaceId));
    const dragged = form.races.find((race) => parseInt(race.id) === parseInt(event.dataTransfer.getData('id')));

    if (droppedOn.id === dragged.id) {
        return;
    }

    const droppedIndex = form.races.indexOf(droppedOn);
    const draggedIndex = form.races.indexOf(dragged);
    form.races = moveRace(draggedIndex, droppedIndex);

    form.races.map((race, index) => {
        race.order = index + 1;
    });
}

function moveRace (from, to) {
    const copy = [ ...form.races ];
    const raceToMove = copy.splice(from, 1)[0];
    copy.splice(to, 0, raceToMove);
    return copy;
}
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
