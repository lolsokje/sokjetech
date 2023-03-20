<template>
    <BackLink :backTo="route('seasons.races.index', [season])" label="race overview"/>

    <form @submit.prevent="form.post(route('seasons.races.store', [season]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" :placeholder="placeholder" class="form-control" type="text">
        </div>

        <SearchableDropdown :items="circuits" label="Select a circuit" text-key="name" @selected="setCircuit"/>

        <h4>Stints</h4>

        <button class="btn btn-primary my-3" @click.prevent="addStint(form.stints)">Add stint</button>

        <button class="btn btn-link text-decoration-underline" @click.prevent="showDialog()">
            or search for existing stints
        </button>

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
                <th colspan="2"></th>
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
                <td class="small-centered">
                    <span v-if="form.stints.length > 1" class="btn btn-link" role="button"
                          @click="deleteStint(stint.order)"
                    >delete
                    </span>
                </td>
                <td class="small-centered">
                    <button class="btn btn-link" @click.prevent="copyStint(stint.order, form.stints)">
                        copy
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" type="submit">Save race</button>
    </form>

    <StintFilterModal ref="stintFilterModal" @selected="selected"/>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import BackLink from '@/Shared/BackLink.vue';
import { defaultStint } from '@/Composables/useDefaultStint';
import { addStint, copyStint, getLastStintOrder } from '@/Composables/useEditStint';
import StintFilterModal from '@/Components/StintFilterModal.vue';
import { ref } from 'vue';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    circuits: {
        type: Array,
        required: true,
    },
});

const placeholder = `${props.season.year} Example Grand Prix`;

const stintFilterModal = ref();

const form = useForm({
    name: '',
    circuit_id: '',
    stints: [ defaultStint ],
});

function deleteStint (number) {
    form.stints = form.stints.filter((stint) => stint.order !== number);

    form.stints.forEach((stint, index) => {
        stint.order = index + 1;
    });
}

function setCircuit (circuit) {
    form.circuit_id = circuit ? circuit.id : '';
}

const showDialog = () => {
    stintFilterModal.value.showDialog();
};

const selected = (stint) => {
    stint.order = getLastStintOrder(form.stints) + 1;
    form.stints.push(stint);
};
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
