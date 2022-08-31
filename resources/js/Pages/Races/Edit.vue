<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="race overview"/>

    <form @submit.prevent="form.put(route('seasons.races.update', [season, race]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" type="text" :placeholder="placeholder">
        </div>

        <SearchableDropdown
            :items="circuits" :selected-item="selectedCircuit" label="Select a circuit" text-key="name"
            @selected="setCircuit"
        />

        <h4>Stints</h4>

        <button class="btn btn-primary my-3" @click.prevent="addStint">Add stint</button>

        <table class="table">
            <thead>
            <tr class="text-center">
                <th>Stint</th>
                <th>Min RNG</th>
                <th>Max RNG</th>
                <th>DNF chance</th>
                <th>Use team rating</th>
                <th>Use driver rating</th>
                <th>Use engine rating</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="stint in form.stints" :key="stint.number" class="text-center">
                <td class="small-centered">{{ stint.order }}</td>
                <td><input v-model="stint.min_rng" class="form-control-sm" type="number"></td>
                <td><input v-model="stint.max_rng" class="form-control-sm" type="number"></td>
                <td><input v-model="stint.reliability" type="checkbox"></td>
                <td><input v-model="stint.use_team_rating" type="checkbox"></td>
                <td><input v-model="stint.use_driver_rating" type="checkbox"></td>
                <td><input v-model="stint.use_engine_rating" type="checkbox"></td>
                <td class="big-centered"><span
                    v-if="form.stints.length > 1" class="text-primary" role="button"
                    @click="deleteStint(stint.order)"
                >delete stint</span></td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" type="submit">Save race</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import SearchableDropdown from '@/Shared/SearchableDropdown';
import Errors from '@/Shared/Errors';
import BackLink from '@/Shared/BackLink';
import { onMounted } from 'vue';
import { defaultStint } from '@/Composables/useDefaultStint';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    circuits: {
        type: Array,
        required: true,
    },
    race: {
        type: Object,
        required: true,
    },
});

const placeholder = `${props.season.year} Example Grand Prix`;

const form = useForm({
    name: props.race.name,
    circuit_id: props.race.circuit_id,
    stints: props.race.stints,
});

const selectedCircuit = props.circuits.find((circuit) => circuit.id === form.circuit_id);

function addStint () {
    const lastOrder = form.stints[form.stints.length - 1].order;
    form.stints.push({
        order: lastOrder + 1,
        min_rng: 0,
        max_rng: 30,
        reliability: false,
        use_team_rating: false,
        use_driver_rating: false,
        use_engine_rating: false,
    });
}

function deleteStint (order) {
    form.stints = form.stints.filter((stint) => stint.order !== order);

    form.stints.forEach((stint, index) => {
        stint.order = index + 1;
    });
}

function setCircuit (circuit) {
    form.circuit_id = circuit ? circuit.id : '';
}

onMounted(() => {
    if (form.stints.length === 0) {
        form.stints.push(defaultStint);
    }
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
