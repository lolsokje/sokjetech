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

<script setup lang="ts">
import { markRaw, onMounted, Ref, ref, watch } from 'vue';
import { InertiaForm, useForm } from '@inertiajs/inertia-vue3';
import Errors from '@/Shared/Errors.vue';
import ThreeSessionElimination from '@/Shared/QualifyingFormats/ThreeSessionElimination.vue';
import SingleSession from '@/Shared/QualifyingFormats/SingleSession.vue';
import SeasonInterface from '@/Interfaces/Season';

interface Props {
    season: SeasonInterface,
    formats: object,
}

interface Form {
    selected_format: string | null,
    format_details: FormatDetails,
}

interface FormatDetails {
    [key: string]: number | string,
}

const props = defineProps<Props>();

const components = {
    three_session_elimination: ThreeSessionElimination,
    single_session: SingleSession,
};

const qualifyingComponent: Ref<object | null> = ref(null);
const format: Ref<string | null> = ref(null);

const form: InertiaForm<Form> = useForm({
    selected_format: null,
    format_details: {},
});

const handleFormatDetailsUpdate = (formatDetails: FormatDetails): void => {
    form.format_details = {};
    for (const [ key, value ] of Object.entries(formatDetails)) {
        form.format_details[key] = value;
    }
};

const pascalCaseToSnakeCase = (string: string): string => {
    return string
        .split(/(?=[A-Z])/) // split at uppercase letters
        .join('_') // join with underscores
        .toLowerCase();
};

const seasonHasStarted = (): boolean => {
    return props.season.started;
};

watch(format, (newFormat) => {
    qualifyingComponent.value = markRaw(components[newFormat]);
    form.selected_format = newFormat;
});

onMounted(() => {
    if (!props.season.format_type) {
        return;
    }

    format.value = pascalCaseToSnakeCase(props.season.format_type.split('\\').at(-1));

    form.selected_format = format.value;

    qualifyingComponent.value = markRaw(components[form.selected_format]);
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
