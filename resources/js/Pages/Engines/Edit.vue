<template>
    <BackLink :backTo="route('series.engines.index', [series])" label="engine overview"/>

    <form class="form-narrow" @submit.prevent="form.put(route('series.engines.update', [series, engine]))">
        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" required type="text">
        </div>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" v-model="form.shared" id="shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button class="btn btn-primary" type="submit">Save engine</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import BackLink from '@/Shared/BackLink.vue';

const props = defineProps({
    series: {
        type: Object,
        required: true,
    },
    engine: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.engine.name,
    shared: props.engine.shared,
});
</script>

<script>
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
