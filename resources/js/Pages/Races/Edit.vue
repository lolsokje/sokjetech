<template>
    <Breadcrumb :link="route('seasons.races.index', season)"
                :linkText="season.full_name"
                :label="race.name"
                append="Edit race"
    />

    <form @submit.prevent="form.put(route('seasons.races.update', [season, race]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" type="text" :placeholder="placeholder">
        </div>

        <div class="row">
            <SearchableDropdown
                    class="col-6"
                    :items="circuits.data"
                    :selected-item="selectedCircuit"
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

        <button class="btn btn-primary" type="submit">Save race</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import Errors from '@/Shared/Errors.vue';
import { Ref, ref } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ClimateSelect from '@/Components/ClimateSelect.vue';
import SeasonInterface from '@/Interfaces/Season';
import CircuitCollection from '@/Interfaces/Circuit/CircuitCollection';
import { Race } from '@/Interfaces/Race';
import Circuit from '@/Interfaces/Circuit';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';
import Climate from '@/Interfaces/Climate';

interface Props {
    season: SeasonInterface,
    circuits: CircuitCollection,
    race: Race,
    climates: Climate[],
}

interface Form {
    name: string,
    circuit_id: string,
    circuit_variation_id: string,
    climate_id: string,
}

const props = defineProps<Props>();

const placeholder = `${props.season.year} Example Grand Prix`;

const form = useForm<Form>({
    name: props.race.name,
    circuit_id: props.race.circuit_id,
    circuit_variation_id: props.race.circuit_variation_id,
    climate_id: props.race.climate_id,
});

const selectedCircuit: Circuit = props.circuits.data.find((circuit) => circuit.id === form.circuit_id);
const variations: Ref<CircuitVariation[]> = ref(selectedCircuit.variations);

const setCircuit = (circuit: Circuit): void => {
    form.circuit_id = circuit ? circuit.id : '';

    form.climate_id = circuit.default_climate.id;

    variations.value = circuit.variations;

    form.circuit_variation_id = variations.value[0].id;
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
