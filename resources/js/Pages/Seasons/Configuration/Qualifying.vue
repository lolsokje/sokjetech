<template>
    <form @submit.prevent="form.post(route('seasons.configuration.qualifying.store', [season]));">
        <Errors :errors="form.errors"/>

        <div class="alert alert-warning mb-3" v-if="seasonHasStarted()">
            The season has started, therefore the qualifying format can no longer be modified
        </div>

        <div class="mb-3">
            <label for="format" class="form-label">Qualifying format</label>
            <select id="format" v-model="format" class="form-control" required :disabled="seasonHasStarted()">
                <option v-for="(format, key) in formats" :key="key" :value="key">{{ format }}</option>
            </select>
        </div>

        <component
            :is="qualifyingComponent" @updateFormatDetails="handleFormatDetailsUpdate"
            :existingFormatDetails="props.season?.format" :seasonStarted="seasonHasStarted()"
        />

        <button type="submit" class="btn btn-primary" v-if="!seasonHasStarted()">Save</button>
    </form>
</template>

<script setup>
import { markRaw, onMounted, ref, watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors';
import ThreeSessionElimination from '@/Shared/QualifyingFormats/ThreeSessionElimination';
import SingleSession from '@/Shared/QualifyingFormats/SingleSession';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    formats: {
        type: Object,
        required: true,
    },
});

const components = {
    three_session_elimination: ThreeSessionElimination,
    single_session: SingleSession,
};

const qualifyingComponent = ref(null);
const format = ref(null);

const form = useForm({
    selected_format: null,
    format_details: {},
});

onMounted(() => {
    if (!props.season.format_type) {
        return;
    }

    format.value = pascalCaseToSnakeCase(props.season.format_type.split('\\').at(-1));

    form.selected_format = format.value;

    qualifyingComponent.value = markRaw(components[form.selected_format]);
});

const handleFormatDetailsUpdate = (formatDetails) => {
    form.format_details = {};
    for (const [ key, value ] of Object.entries(formatDetails)) {
        form.format_details[key] = value;
    }
};

const pascalCaseToSnakeCase = (string) => {
    return string
        .split(/(?=[A-Z])/) // split at uppercase letters
        .join('_') // join with underscores
        .toLowerCase();
};

watch(format, (newFormat) => {
    qualifyingComponent.value = markRaw(components[newFormat]);
    form.selected_format = newFormat;
});

const seasonHasStarted = () => {
    return props.season.started;
};
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
