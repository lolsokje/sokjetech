<template>
    <Breadcrumb :link="route('seasons.races.index', season)" :linkText="season.full_name" label="Create race"/>

    <form @submit.prevent="form.post(route('seasons.races.store', [season]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" :placeholder="placeholder" class="form-control" type="text">
        </div>

        <div class="row">
            <SearchableDropdown
                    class="col-6"
                    :items="circuits.data"
                    label="Select a circuit"
                    text-key="name"
                    @selected="setCircuit"
            />

            <div class="col-6">
                <label for="variation" class="form-label">Select a variation</label>
                <select id="variation" class="form-select" v-model="form.circuit_variation_id" required>
                    <option v-for="variation in variations" :key="variation.id" :value="variation.id">
                        {{ variation.name }} ({{ variation.length.km }}km/{{ variation.length.m }}m -
                        {{ variation.laptime.readable }})
                    </option>
                </select>
            </div>
        </div>

        <ClimateSelect :climates="climates" v-model="form.climate_id"/>

        <h4>Stints</h4>

        <button class="btn btn-primary my-3" @click.prevent="addStint(form.stints)">Add stint</button>

        <button class="btn btn-link text-decoration-underline" @click.prevent="showDialog()">
            or search for existing stints
        </button>

        <div class="mb-3">
            <input type="checkbox"
                   class="form-check-inline"
                   id="select_all"
                   v-model="selectAll"
                   @change.prevent="selectAllRolls()"
            >
            <label for="select_all" class="form-check-label">Select all RNG rolls for all stints</label>
        </div>

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
            <tr v-for="stint in form.stints" :key="stint.order" class="text-center">
                <td class="small-centered">{{ stint.order }}</td>
                <td class="small-centered"><input v-model="stint.min_rng" class="form-control" type="number"></td>
                <td class="small-centered"><input v-model="stint.max_rng" class="form-control" type="number"></td>
                <td>
                    <input v-model="stint.reliability"
                           type="checkbox"
                           @change.prevent="checkSelectAllRollsCheckbox(stint.reliability)"
                    >
                </td>
                <td>
                    <input v-model="stint.use_team_rating"
                           type="checkbox"
                           @change.prevent="checkSelectAllRollsCheckbox(stint.use_team_rating)"
                    >
                </td>
                <td>
                    <input v-model="stint.use_driver_rating"
                           type="checkbox"
                           @change.prevent="checkSelectAllRollsCheckbox(stint.use_driver_rating)"
                    >
                </td>
                <td>
                    <input v-model="stint.use_engine_rating"
                           type="checkbox"
                           @change.prevent="checkSelectAllRollsCheckbox(stint.use_engine_rating)"
                    >
                </td>
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

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import { defaultStint } from '@/Composables/useDefaultStint.js';
import { addStint, copyStint, getLastStintOrder } from '@/Composables/useEditStint.js';
import StintFilterModal from '@/Components/StintFilterModal.vue';
import { Ref, ref } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ClimateSelect from '@/Components/ClimateSelect.vue';
import Circuit from '@/Interfaces/Circuit';
import Climate from '@/Interfaces/Climate';
import SeasonInterface from '@/Interfaces/Season';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';
import RaceStint from '@/Interfaces/RaceStint';
import CircuitCollection from '@/Interfaces/Circuit/CircuitCollection';

interface Props {
    season: SeasonInterface,
    circuits: CircuitCollection,
    climates: Climate[],
}

interface Form {
    name: string,
    circuit_id: string,
    circuit_variation_id: string,
    stints: RaceStint[],
    climate_id: string,
}

const props = defineProps<Props>();

const placeholder = `${props.season.year} Example Grand Prix`;

const stintFilterModal = ref();

const form = useForm<Form>({
    name: '',
    circuit_id: '',
    circuit_variation_id: '',
    stints: [ defaultStint ] as RaceStint[],
    climate_id: '',
});

const variations: Ref<CircuitVariation[]> = ref([]);
const selectAll: Ref<boolean> = ref(false);

const deleteStint = (number: number): void => {
    form.stints = form.stints.filter((stint) => stint.order !== number);

    form.stints.forEach((stint, index) => {
        stint.order = index + 1;
    });
};

const setCircuit = (circuit: Circuit): void => {
    form.circuit_id = circuit ? circuit.id : '';

    form.climate_id = circuit.default_climate.id;

    variations.value = circuit.variations;

    form.circuit_variation_id = variations.value[0].id;
};

const showDialog = () => {
    stintFilterModal.value.showDialog();
};

const selected = (stint: RaceStint): void => {
    stint.order = getLastStintOrder(form.stints) + 1;
    form.stints.push(stint);
};

const selectAllRolls = (): void => {
    form.stints.forEach((stint) => {
        stint.reliability = true;
        stint.use_team_rating = true;
        stint.use_driver_rating = true;
        stint.use_engine_rating = true;
    });
};

const checkSelectAllRollsCheckbox = (checked: boolean): void => {
    if (! checked) {
        selectAll.value = false;
    }
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
