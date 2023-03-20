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

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    universe: Universe,
}

defineProps<Props>();

const form: InertiaForm<{
    name: string,
}> = useForm({
    name: '',
});
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
