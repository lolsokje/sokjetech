<template>
    <BackLink :backTo="route('universes.drivers.index', [universe])" label="driver overview"/>

    <form class="form-narrow" @submit.prevent="form.post(route('universes.drivers.store', universe))">
        <Errors :errors="form.errors"/>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label" for="first_name">First name</label>
                <input id="first_name" v-model="form.first_name" class="form-control" required type="text">
            </div>

            <div class="col">
                <label class="form-label" for="last_name">Last name</label>
                <input id="last_name" v-model="form.last_name" class="form-control" required type="text">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label w-25" for="dob">Date of birth</label>
                <input id="dob" v-model="form.dob" class="form-control" required type="date">
            </div>

            <CountrySelect class="col" label="Country of birth" @countryChanged="setCountry"/>
        </div>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" v-model="form.shared" id="shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button class="btn btn-primary" type="submit">Save driver</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors.vue';
import CountrySelect from '@/Shared/CountrySelect.vue';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    universe: Universe,
}

defineProps<Props>();

const form = useForm({
    first_name: '',
    last_name: '',
    dob: '',
    country: '',
    shared: false,
});

const setCountry = (country: string): void => {
    form.country = country;
};
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
