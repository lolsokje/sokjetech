<template>
    <Breadcrumb :link="route('circuits.edit', circuit)"
                :linkText="circuit.name"
                label="Variations"
                :append="variation.name"
    />

    <form class="form-narrow" @submit.prevent="form.put(route('circuits.variations.update', [circuit, variation]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" v-model="form.name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="length" class="form-label">Length (in meters)</label>
            <input id="length" type="number" v-model="form.length" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="base_laptime" class="form-label">Base laptime (in the format mm:ss.xxx)</label>
            <input id="base_laptime" type="text" v-model="form.base_laptime" class="form-control" required>
        </div>

        <RatingMultipliers v-model:team="form.team_multiplier"
                           v-model:engine="form.engine_multiplier"
        />

        <button class="btn btn-primary">Save</button>
    </form>
</template>

<script lang="ts" setup>
import Circuit from '@/Interfaces/Circuit';
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';
import RatingMultipliers from '@/Components/Form/Circuit/RatingMultipliers.vue';

interface Props {
    circuit: Circuit,
    variation: CircuitVariation,
}

interface Form {
    name: string,
    length: number,
    base_laptime: string,
    team_multiplier: number,
    engine_multiplier: number,
}

const props = defineProps<Props>();

const form = useForm<Form>({
    name: props.variation.name,
    length: props.variation.length.default,
    base_laptime: props.variation.laptime.readable,
    team_multiplier: props.variation.multipliers.team,
    engine_multiplier: props.variation.multipliers.engine,
});
</script>
