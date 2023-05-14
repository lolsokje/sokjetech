<template>
    <Breadcrumb :link="route('circuits.index')" linkText="Circuits" :label="circuit.name" append="Edit circuit"/>

    <form class="form-narrow" @submit.prevent="form.put(route('circuits.update', circuit))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <CountrySelect :country="form.country" @countryChanged="setCountry"/>

        <div class="mb-3">
            <label class="form-label" for="default_climate">Default climate</label>
            <select id="default_climate"
                    v-model="form.default_climate_id"
                    class="form-select"
                    @change.prevent="setCondition()"
            >
                <option v-for="climate in climates" :key="climate.id" :value="climate.id">{{ climate.name }}</option>
            </select>
        </div>

        <div class="mb-3" v-if="conditions">
            <h5>Condition chances</h5>
            <table class="table table-narrow">
                <thead>
                <tr>
                    <th v-for="condition in conditions" :key="condition.id">{{ condition.condition_name }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="smallest-centered" v-for="condition in conditions" :key="condition.id">
                        {{ condition.chance }}%
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

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
import { onMounted, ref, Ref } from 'vue';

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

const conditions: Ref<WeatherConditions> = ref(props.climates[0].conditions);

function setCountry (country: string) {
    form.country = country;
}

const setCondition = (): void => {
    conditions.value = props.climates.find((c: Climate) => c.id === form.default_climate_id).conditions;
};

onMounted(() => setCondition());
</script>
