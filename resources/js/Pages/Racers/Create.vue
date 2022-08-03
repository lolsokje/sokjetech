<template>
    <BackLink :backTo="route('seasons.entrants.index', [season])" label="entrant overview"/>

    <h2>{{ entrant.full_name }}'s drivers</h2>

    <form class="form-narrow" @submit.prevent="form.post(route('seasons.racers.store', [season, entrant]))">
        <Errors :errors="form.errors"/>

        <p v-if="hasError" class="text-danger">This driver or number has already been selected</p>
        <SearchableDropdown :items="drivers" :selected-item="state.driver"
                            label="Select a driver" text-key="full_name" @selected="setDriver"/>

        <div class="mb-3 col-6">
            <label for="number">Driver number</label>
            <input id="number" v-model="state.number" class="form-control" type="number">
        </div>

        <button class="btn btn-primary" type="button" @click.prevent="addDriver">Add driver</button>

        <p class="mt-3">Current drivers</p>
        <table class="table table-bordered table-dark my-3">
            <thead>
            <tr>
                <th>Driver</th>
                <th>Number</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in form.drivers" :key="driver.id">
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="small-centered">
                    <input type="number" class="form-control text-center" v-model="driver.number">
                </td>
                <td class="small-centered">
                    <button class="btn btn-link p-0" role="button" @click.prevent="removeDriver(driver.id)">
                        delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" type="submit">Save drivers</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, reactive, ref } from 'vue';
import SearchableDropdown from '@/Shared/SearchableDropdown';
import Errors from '@/Shared/Errors';
import BackLink from '@/Shared/BackLink';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    entrant: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Array,
        required: true,
    },
    numbers: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    drivers: [],
});

const hasError = ref(false);
const usedDriverNumbers = ref([]);
const drivers = ref(props.drivers);

const state = reactive({
    driver: {},
    number: null,
});

function removeDriver (id) {
    const driver = form.drivers.find((driver) => driver.id === id);
    drivers.value.push(driver);
    form.drivers = form.drivers.filter((driver) => driver.id !== id);
    usedDriverNumbers.value = usedDriverNumbers.value.filter((number) => number !== driver.number);
}

function setDriver (driver) {
    state.driver = driver;
}

function addDriver () {
    hasError.value = false;

    const duplicateDriver = form.drivers.find((driver) => driver.driver_id === state.driver.id) !== undefined;
    const duplicateNumber = form.drivers.find((driver) => driver.number === state.number) !== undefined || usedDriverNumbers.value.includes(state.number);

    if (duplicateDriver || duplicateNumber) {
        hasError.value = true;
        clearForm();
        return;
    }

    form.drivers.push({
        id: state.driver.id,
        driver_id: state.driver.id,
        full_name: state.driver.full_name,
        number: state.number,
    });

    drivers.value = drivers.value.filter((driver) => driver.id !== state.driver.id);

    getUsedDriverNumbers();
    clearForm();
}

function clearForm () {
    state.driver = {};
    state.number = null;
}

function getUsedDriverNumbers () {
    usedDriverNumbers.value = form.drivers.map((driver) => driver.number);
}

onMounted(() => {
    form.drivers = props.entrant.active_racers.map((driver) => {
        return getDriverInformation(driver);
    });

    getUsedDriverNumbers();
});

function getDriverInformation (driver) {
    return {
        id: driver.id,
        driver_id: driver.driver.id,
        full_name: driver.driver.full_name,
        number: driver.number,
        active: driver.active,
    };
}
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
