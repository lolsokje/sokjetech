<template>
    <BackLink :backTo="route('universes.series.index', [universe])" label="series overview"/>

    <form class="form-narrow" @submit.prevent="form.put(route('universes.series.update', [universe, series]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <button class="btn btn-primary" type="submit">Save series</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors';
import BackLink from '@/Shared/BackLink';

const props = defineProps({
    universe: {
        type: Object,
        required: true,
    },
    series: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.series.name,
});
</script>

<script>
import Universe from '@/Layouts/Universe';

export default { layout: Universe };
</script>
