<template>
    <h3>Submit a suggestion</h3>

    <form @submit.prevent="form.post(route('suggestions.store'))">
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
            <label for="summary" class="form-label">Please provide a short summary of the suggestion</label>
            <input type="text" v-model="form.summary" id="summary" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="details" class="form-label">
                Please provide as many details about your suggestion. The more detail you add, the more likely it will
                be implemented
            </label>
            <textarea v-model="form.details" id="details" rows="7" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="!validForm">Submit suggestion</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import Errors from '@/Shared/Errors.vue';
import { feedbackTypes } from '@/Composables/useFeedbackTypes';

const form = useForm({
    type: '',
    summary: '',
    details: '',
});

const validForm = computed(() => form.type !== '' && form.summary !== '' && form.details !== '');
</script>
