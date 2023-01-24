<template>
    <h1>Create circuit</h1>

    <form class="form-narrow" @submit.prevent="form.post(route('circuits.store'))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <CountrySelect @countryChanged="setCountry"/>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" id="shared" v-model="form.shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</template>

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';

const form: InertiaForm<{
    name: string | null,
    country: string | null,
    shared: boolean
}> = useForm({
    name: null,
    country: null,
    shared: false,
});

function setCountry (country: string) {
    form.country = country;
}
</script>
