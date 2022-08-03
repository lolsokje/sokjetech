<template>
    <BackLink :backTo="route('universes.series.index', [universe])" label="series overview"/>

    <form @submit.prevent="form.post(route('universes.series.store', [universe]))" class="form-narrow">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Save series</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors';
import BackLink from '@/Shared/BackLink';

defineProps({
    universe: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: '',
});
</script>

<script>
import Universe from '@/Layouts/Universe';

export default { layout: Universe };
</script>
