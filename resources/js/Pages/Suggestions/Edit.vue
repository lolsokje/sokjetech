<template>
    <h3>
        Suggestion |
        <span class="badge badge-pill" :class="getStatusClass(suggestion.status)">{{ suggestion.status_text }}</span> |
        {{ suggestion.type }}
    </h3>
    <h4 class="text-muted">{{ suggestion.summary }}</h4>

    <p class="mb-0"><strong>Details:</strong></p>
    <p>{{ suggestion.details }}</p>

    <template v-if="suggestion.admin_remarks">
        <p class="mb-0"><strong>Admin remarks</strong></p>
        <p>{{ suggestion.admin_remarks }}</p>
    </template>

    <form @submit.prevent="form.put(route('suggestions.update', [suggestion]))" v-if="can.edit">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select v-model="form.status" id="status" class="form-control" required>
                <option v-for="(status, index) in SuggestionStatus" :key="index" :value="status.toLowerCase()">
                    {{ status }}
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="admin_remarks" class="form-label">Admin remarks</label>
            <textarea v-model="form.admin_remarks" id="admin_remarks" rows="7" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update suggestion</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { SuggestionStatus } from '@/Enums/SuggestionStatus';
import { getStatusClass } from '@/Composables/useStatusClasses';

const props = defineProps({
    suggestion: Object,
    can: Object,
});

const form = useForm({
    status: props.suggestion.status,
    admin_remarks: props.suggestion.admin_remarks,
});
</script>
