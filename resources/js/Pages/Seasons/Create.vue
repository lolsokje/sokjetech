<template>
    <Breadcrumb :link="route('series.seasons.index', series)" :linkText="series.name" label="Create season"/>

    <form @submit.prevent="form.post(route('series.seasons.store', [series]))" class="form-narrow">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" id="year" v-model="form.year" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Season name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full name</label>
            <input type="text" id="full_name" :value="`${form.year} ${form.name}`" class="form-control" disabled>
        </div>

        <button type="submit" class="btn btn-primary">Save season</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    series: Series,
}

const props = defineProps<Props>();

const form = useForm({
    year: '',
    name: `${props.series.name} Season`,
});
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
