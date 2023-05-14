<template>
    <Breadcrumb :link="route('circuits.index')" linkText="Circuits" label="Create circuit"/>

    <form class="form-narrow" @submit.prevent="form.post(route('circuits.store'))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <CountrySelect @countryChanged="setCountry"/>

        <ClimateSelect :climates="climates" v-model="form.default_climate_id"/>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" id="shared" v-model="form.shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</template>

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Climate from '@/Interfaces/Climate';
import ClimateSelect from '@/Components/ClimateSelect.vue';

interface Props {
    climates: Climate[],
}

const props = defineProps<Props>();

const form: InertiaForm<{
    name: string | null,
    country: string | null,
    shared: boolean,
    default_climate_id: number | null,
}> = useForm({
    name: null,
    country: null,
    shared: false,
    default_climate_id: props.climates[0].id,
});

function setCountry (country: string) {
    form.country = country;
}
</script>
