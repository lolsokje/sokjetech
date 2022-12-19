<template>
    <div class="row mb-3">
        <div class="col-4">
            <label for="runs_per_session" class="form-label">Runs per session</label>
            <input
                type="number" class="form-control" id="runs_per_session" v-model="formatDetails.runs_per_session"
                required :disabled="seasonStarted"
            >
        </div>

        <div class="col-4">
            <label for="min_rng" class="form-label">Min RNG per run</label>
            <input type="number" class="form-control" id="min_rng" v-model="formatDetails.min_rng" required
                   :disabled="seasonStarted"
            >
        </div>

        <div class="col-4">
            <label for="max_rng" class="form-label">Max RNG per run</label>
            <input type="number" class="form-control" id="max_rng" v-model="formatDetails.max_rng" required
                   :disabled="seasonStarted"
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, watch } from 'vue';
import { assignFormatDetails } from '@/Composables/useQualifyingFormat.js';

interface FormatDetails {
    runs_per_session: number | null,
    min_rng: number | null,
    max_rng: number | null,
}

interface Props {
    existingFormatDetails?: FormatDetails,
    seasonStarted: boolean,
}

const props = defineProps<Props>();

const formatDetails: FormatDetails = reactive({
    runs_per_session: null,
    min_rng: null,
    max_rng: null,
});

const emit = defineEmits([ 'updateFormatDetails' ]);

const handleFormatDetailsUpdate = (): void => {
    emit('updateFormatDetails', formatDetails);
};

watch(formatDetails, () => {
    handleFormatDetailsUpdate();
});

onMounted(() => {
    assignFormatDetails(formatDetails, props?.existingFormatDetails);
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
