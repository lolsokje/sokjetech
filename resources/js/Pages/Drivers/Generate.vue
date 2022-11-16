<template>
    <BackLink :backTo="route('universes.drivers.index', [universe])" label="driver overview"/>

    <h3>Generate drivers for "{{ universe.name }}"</h3>

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

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import BackLink from '@/Shared/BackLink.vue';
import Errors from '@/Shared/Errors.vue';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    universe: Object,
    languages: Object,
});

const sortedLanguages = ref([]);
const drivers = ref([]);
const processing = ref(false);

const form = useForm({
    language: null,
    gender: null,
    start: null,
    end: null,
    amount: 10,
});

const buttonText = computed(() => form.amount === 1 ? 'driver' : 'drivers');
const formValid = computed(() => {
    return form.start !== null && form.end !== null && form.amount > 0;
});

const generateDrivers = async () => {
    const response = await axios.post(route('universes.drivers.generate', [ props.universe ]), form);
    drivers.value = await response.data;
};

const rejectDriver = (index) => {
    drivers.value.splice(index, 1);
};

const persistDrivers = async () => {
    processing.value = true;

    Inertia.post(route('universes.drivers.persist', [ props.universe ]), {
        drivers: drivers.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
    drivers.value = [];

    processing.value = false;
};

onMounted(() => {
    for (const [ locale, language ] of Object.entries(props.languages)) {
        sortedLanguages.value.push({
            locale,
            language,
        });
    }

    sortedLanguages.value.sort((a, b) => {
        return a.language < b.language ? -1 : 1;
    });
});
</script>

<script>
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
