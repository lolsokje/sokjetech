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

const props = defineProps<{
    circuit: Circuit,
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
