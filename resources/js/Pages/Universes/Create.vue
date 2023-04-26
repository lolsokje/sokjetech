<template>
    <Breadcrumb :link="route('universes.index')" linkText="Universes" label="Create universe"/>

    <form @submit.prevent="form.post(route('universes.store'))" class="form-narrow">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" v-model="form.name" required>
        </div>

        <div class="mb-3">
            <label for="visibility" class="form-label">Visibility</label>
            <select v-model="form.visibility" id="visibility" class="form-select" required>
                <option value="">Select an option</option>
                <option v-for="(visibility, id) in visibilities" :value="id" :key="id">{{ visibility }}</option>
            </select>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</template>

<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    visibilities: { [key: number]: string };
}

const props = defineProps<Props>();

const form: InertiaForm<{
    name: string | null,
    visibility: number | string,
}> = useForm({
    name: null,
    visibility: '',
});
</script>
