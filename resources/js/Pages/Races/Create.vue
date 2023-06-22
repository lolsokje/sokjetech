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

        <button class="btn btn-primary" type="submit">Save race</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import { Ref, ref } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ClimateSelect from '@/Components/ClimateSelect.vue';
import Circuit from '@/Interfaces/Circuit';
import Climate from '@/Interfaces/Climate';
import SeasonInterface from '@/Interfaces/Season';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';
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
    climate_id: string,
}

const props = defineProps<Props>();

const placeholder = `${props.season.year} Example Grand Prix`;

const form = useForm<Form>({
    name: '',
    circuit_id: '',
    circuit_variation_id: '',
    climate_id: '',
});

const variations: Ref<CircuitVariation[]> = ref([]);

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
