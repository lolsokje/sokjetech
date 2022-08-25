<template>
    <h3>
        Bug |
        <span class="badge badge-pill" :class="getStatusClass(bug.status)">{{ bug.status_text }}</span> |
        {{ bug.type }}
        <template v-if="can.edit && bug.app_version">
            | v{{ bug.app_version }}
        </template>
    </h3>
    <h4 class="text-muted">{{ bug.summary }}</h4>

    <p class="mb-0"><strong>Details:</strong></p>
    <p>{{ bug.details }}</p>

    <template v-if="bug.admin_remarks">
        <p class="mb-0"><strong>Admin remarks</strong></p>
        <p>{{ bug.admin_remarks }}</p>
    </template>

    <form @submit.prevent="form.put(route('bugs.update', [bug]))" v-if="can.edit">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select v-model="form.status" id="status" class="form-control" required>
                <option v-for="(status, index) in labelMap" :key="index" :value="status.toLowerCase()">
                    {{ status }}
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="admin_remarks" class="form-label">Admin remarks</label>
            <textarea v-model="form.admin_remarks" id="admin_remarks" rows="7" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update bug</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { getStatusClass } from '@/Composables/useStatusClasses';
import { labelMap } from '@/Enums/BugStatus';

const props = defineProps({
    bug: Object,
    can: Object,
});

const form = useForm({
    status: props.bug.status,
    admin_remarks: props.bug.admin_remarks,
});
</script>
