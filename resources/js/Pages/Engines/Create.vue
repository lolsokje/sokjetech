<template>
    <BackLink :backTo="route('series.engines.index', [series])" label="engine overview"/>

    <form @submit.prevent="form.post(route('series.engines.store', [series]))" class="form-narrow">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" required>
        </div>

        <div class="mb-3">
            <input type="checkbox" class="form-check-inline" v-model="form.shared" id="shared">
            <label for="shared" class="form-check-label">Share with others</label>
        </div>

        <button type="submit" class="btn btn-primary">Save engine</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    series: Series,
}

defineProps<Props>();

const form = useForm({
    name: '',
    shared: false,
});
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
