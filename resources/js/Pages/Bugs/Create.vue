<template>
    <h3>Report a bug</h3>

    <form @submit.prevent="form.post(route('bugs.store'))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select v-model="form.type" id="type" class="form-control" required>
                <option v-for="(type, index) in feedbackTypes()" :key="index" :value="type">
                    {{ type }}
                </option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="summary" class="form-label">Please provide a short summary of the bug</label>
            <input type="text" v-model="form.summary" id="summary" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="details" class="form-label">Please provide as many details about the bug. The more detail you
                add, the more accurately it can be reproduced</label>
            <textarea v-model="form.details" id="details" rows="7" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="!validForm">Report bug</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors';
import { computed } from 'vue';
import { feedbackTypes } from '@/Composables/useFeedbackTypes';

const form = useForm({
    type: '',
    summary: '',
    details: '',
});

const validForm = computed(() => form.type !== '' && form.summary !== '' && form.details !== '');
</script>
