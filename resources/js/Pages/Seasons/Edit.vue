<template>
    <h3>Editing {{ season.full_name }}</h3>

    <BackLink :backTo="route('series.seasons.index', [series])" label="season index"/>

    <form @submit.prevent="form.put(route('series.seasons.update', [series, season]))" class="form-narrow">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" id="year" v-model="form.year" min="0" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Season name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full name example</label>
            <input type="text" id="full_name" :value="`${form.year} ${form.name}`" class="form-control" disabled>
        </div>

        <button type="submit" class="btn btn-primary">Save season</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import BackLink from '@/Shared/BackLink.vue';

interface Props {
    series: Series,
    season: Season,
}

const props = defineProps<Props>();

const form = useForm({
    year: props.season.year,
    name: props.season.name,
});
</script>

<script lang="ts">
import Series from '@/Layouts/Series.vue';

export default { layout: Series };
</script>
