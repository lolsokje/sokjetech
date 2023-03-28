<template>
    <Breadcrumb :link="route('series.engines.index', series)"
                :linkText="series.name"
                :label="engine.name"
                append="Edit engine"
    />

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

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    series: Series,
    engine: Engine,
}

const props = defineProps<Props>();

const form = useForm({
    name: props.engine.name,
    shared: props.engine.shared,
});
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
