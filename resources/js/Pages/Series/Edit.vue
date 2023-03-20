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

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    universe: Universe,
    series: Series,
}

const props = defineProps<Props>();

const form: InertiaForm<{
    name: string,
}> = useForm({
    name: props.series.name,
});
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
