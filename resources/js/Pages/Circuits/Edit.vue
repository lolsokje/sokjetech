<template>
    <Breadcrumb :link="route('circuits.index')" linkText="Circuits" :label="circuit.name" append="Edit circuit"/>

    <form class="form-narrow" @submit.prevent="form.put(route('circuits.update', circuit))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <CountrySelect :country="form.country" @countryChanged="setCountry"/>

        <ClimateSelect :climates="climates" v-model="form.default_climate_id"/>

        <h5>Track variations</h5>

        <InertiaLink :href="route('circuits.variations.create', circuit)" class="btn btn-secondary my-3">
            Add variation
        </InertiaLink>

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th class="text-center">Length</th>
                <th class="text-center">Laptime</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="variation in variations" :key="variation.id">
                <td class="padded-left">{{ variation.name }}</td>
                <td class="medium-centered">{{ variation.length }}</td>
                <td class="medium-centered">{{ variation.readable_laptime }}</td>
                <td class="smallest-centered">
                    <InertiaLink :href="route('circuits.variations.edit', [circuit, variation])">
                        edit
                    </InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" id="shared" v-model="form.shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</template>

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';
import Circuit from '@/Interfaces/Circuit';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Climate from '@/Interfaces/Climate';
import ClimateSelect from '@/Components/ClimateSelect.vue';
import CircuitVariation from '@/Interfaces/CircuitVariation';

const props = defineProps<{
    circuit: Circuit,
    variations: CircuitVariation[],
    climates: Climate[],
}>();

const form: InertiaForm<{
    name: string,
    country: string,
    default_climate_id: string,
    shared: boolean,
}> = useForm({
    name: props.circuit.name,
    country: props.circuit.country,
    default_climate_id: props.circuit.default_climate_id,
    shared: props.circuit.shared,
});

function setCountry (country: string) {
    form.country = country;
}
</script>
