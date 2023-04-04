<template>
    <Breadcrumb :link="route('universes.drivers.index', universe)" :linkText="universe.name" label="Generate drivers"/>

    <form @submit.prevent="generateDrivers">
        <Errors :errors="form.errors"/>

        <div class="row mb-3">
            <div class="col-4">
                <label for="language" class="form-label">Language</label>
                <select v-model="form.language" id="language" class="form-select" required>
                    <option value="null">Random</option>
                    <option v-for="language in sortedLanguages" :key="language.locale" :value="language.locale">
                        {{ language.language }}
                    </option>
                </select>
            </div>

            <div class="col-4">
                <label for="gender" class="form-label">Gender</label>
                <select v-model="form.gender" id="gender" class="form-select" required>
                    <option value="null">Random</option>
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                </select>
            </div>

            <div class="col-4">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" v-model="form.amount" id="amount" class="form-control" min="1" required>
                <small class="text-danger" v-if="form.amount < 1">You need to generate at least one driver</small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label for="start" class="form-label">Earliest date of birth</label>
                <input type="date" v-model="form.start" id="start" class="form-control" required>
            </div>

            <div class="col-6">
                <label for="end" class="form-label">Latest date of birth</label>
                <input type="date" v-model="form.end" id="end" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="!formValid || form.processing || processing">
            Generate {{ form.amount }} {{ buttonText }}
        </button>
    </form>

    <table class="table mt-3" v-if="drivers.length">
        <thead>
        <tr>
            <th>Name</th>
            <th>Date of birth</th>
            <th>Country</th>
            <th>Share?</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(driver, index) in drivers" :key="index">
            <td class="padded-left">{{ driver.first_name }} {{ driver.last_name }}</td>
            <td class="padded-left">{{ driver.dob }}</td>
            <td class="small-centered">
                <CountryFlag :country="driver.country"/>
            </td>
            <td class="small-centered">
                <input type="checkbox" v-model="driver.shared">
            </td>
            <td class="medium-centered">
                <button class="btn btn-link" @click="rejectDriver(index)">reject</button>
            </td>
        </tr>
        </tbody>
    </table>

    <button class="btn btn-success" v-if="drivers.length" @click="persistDrivers" :disabled="processing">Save</button>
</template>

<script setup lang="ts">
import { InertiaForm, router, useForm } from '@inertiajs/vue3';
import Errors from '@/Shared/Errors.vue';
import { computed, onMounted, Ref, ref } from 'vue';
import axios from 'axios';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Language {
    [key: string]: string,
}

interface SortedLanguage {
    language: string,
    locale: string,
}

interface Props {
    universe: Universe,
    languages: Language
}

interface Form {
    language: string | null,
    gender: string | null,
    start: string | null,
    end: string | null,
    amount: number,
}

const props = defineProps<Props>();

const sortedLanguages: Ref<Array<SortedLanguage>> = ref([]);
const drivers: Ref<Array<Driver>> = ref([]);
const processing = ref(false);
const today = new Date();
const start = new Date();

today.setFullYear(today.getFullYear() - 20);
start.setFullYear(today.getFullYear() - 15);

const formatDateForInput = (date: Date): string => {
    return date.toLocaleDateString('en-GB').split('/').reverse().join('-');
};

const form: InertiaForm<Form> = useForm({
    language: null,
    gender: null,
    start: formatDateForInput(today),
    end: formatDateForInput(start),
    amount: 10,
});

const buttonText = computed((): string => form.amount === 1 ? 'driver' : 'drivers');
const formValid = computed((): boolean => {
    return form.start !== null && form.end !== null && form.amount > 0;
});

const generateDrivers = async () => {
    const response = await axios.post(route('universes.drivers.generate', [ props.universe ]), form);
    drivers.value = await response.data;
};

const rejectDriver = (index: number): void => {
    drivers.value.splice(index, 1);
};

const persistDrivers = async () => {
    processing.value = true;

    const payload: RequestPayload = { drivers: drivers.value } as RequestPaylod;

    router.post(route('universes.drivers.persist', [ props.universe ]), payload, {
        preserveState: true,
        preserveScroll: true,
    });
    drivers.value = [];

    processing.value = false;
};

onMounted((): void => {
    for (const [ locale, language ] of Object.entries(props.languages)) {
        sortedLanguages.value.push({
            locale,
            language,
        });
    }

    sortedLanguages.value.sort((a: SortedLanguage, b: SortedLanguage) => {
        return a.language < b.language ? -1 : 1;
    });
});
</script>

<script lang="ts">
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
