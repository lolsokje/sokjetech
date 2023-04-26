<template>
    <Breadcrumb :link="route('seasons.entrants.index', season)"
                :linkText="season.full_name"
                :label="entrant.full_name"
                :labelLink="route('seasons.entrants.edit', [season, entrant])"
                append="Drivers"
    />


    <form class="form-narrow" @submit.prevent="form.post(route('seasons.racers.store', [season, entrant]))">
        <Errors :errors="form.errors"/>

        <p v-if="hasError" class="text-danger">This driver or number has already been selected</p>
        <SearchableDropdown :items="availableDrivers" :selected-item="state.driver"
                            label="Select a driver" text-key="full_name_with_age" @selected="setDriver"
        />

        <div class="mb-3 col-6">
            <label for="number">Driver number</label>
            <input id="number" v-model="state.number" class="form-control" type="number">
        </div>

        <button class="btn btn-secondary" type="button" @click.prevent="addDriver">Add driver</button>

        <p class="mt-3">Current drivers</p>
        <table class="table my-3">
            <thead>
            <tr>
                <th>Driver</th>
                <th>Age</th>
                <th>Number</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="driver in form.drivers" :key="driver.driver_id">
                <td class="padded-left">{{ driver.full_name }}</td>
                <td class="smallest-centered">{{ driver.age }}</td>
                <td class="small-centered">
                    <input type="number" class="form-control text-center" v-model="driver.number">
                </td>
                <td class="small-centered">
                    <button class="btn btn-link p-0" role="button" @click.prevent="removeDriver(driver.driver_id)">
                        delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" type="submit">Save drivers</button>
    </form>
</template>

<script setup lang="ts">
import Errors from '@/Shared/Errors.vue';
import SearchableDropdown from '@/Shared/SearchableDropdown.vue';
import { InertiaForm, useForm } from '@inertiajs/vue3';
import SeasonInterface from '@/Interfaces/Season';
import Entrant from '@/Interfaces/Entrant';
import { onMounted, reactive, ref, Ref } from 'vue';
import Driver from '@/Interfaces/Driver';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Racer from '@/Interfaces/Racer';

interface FormDriver {
    driver_id: string,
    full_name: string,
    number: number,
    active: boolean,
}

interface Props {
    season: SeasonInterface,
    entrant: Entrant,
    drivers: Driver[],
    numbers: number[],
}

interface State {
    driver?: Driver,
    number: number,
}

const props = defineProps<Props>();

const form: InertiaForm<{
    drivers: Racer[],
}> = useForm({
    drivers: props.entrant.active_racers,
});

const state: State = reactive({
    driver: {},
    number: 0,
});

const hasError: Ref<boolean> = ref(false);
const usedDriverNumbers: Ref<number[]> = ref([]);
const availableDrivers: Ref<Driver[]> = ref(props.drivers);

const setDriver = (driver: Driver): void => {
    state.driver = driver;
};

const addDriver = (): void => {
    hasError.value = false;

    if (! state.driver) {
        return;
    }

    const duplicateDriver = form.drivers.find((driver: FormDriver) => driver.driver_id === state.driver?.id);
    const duplicateNumber = usedDriverNumbers.value.find((number: number | undefined) => number === state.number);

    if (duplicateDriver || duplicateNumber !== undefined) {
        hasError.value = true;
        clearForm();
        return;
    }

    form.drivers.push({
        driver_id: state.driver.id,
        full_name: state.driver.full_name,
        age: state.driver.age,
        number: state.number,
        active: true,
    });

    availableDrivers.value = availableDrivers.value.filter((driver: Driver) => driver.id !== state.driver?.id);

    setUsedDriverNumbers();
    clearForm();
};

const removeDriver = (id: string): void => {
    const driver = form.drivers.find((driver: FormDriver) => driver.driver_id === id);

    if (! driver) {
        return;
    }

    driver.full_name_with_age = `${driver.full_name} (${driver.age})`;

    form.drivers = form.drivers.filter((driver: FormDriver) => driver.driver_id !== id);
    availableDrivers.value.push(driver as Driver);

    setUsedDriverNumbers();
};

const clearForm = (): void => {
    state.driver = undefined;
    state.number = 0;
};

const setUsedDriverNumbers = () => {
    usedDriverNumbers.value = form.drivers.map((driver: FormDriver) => driver.number);
};

onMounted(() => {
    setUsedDriverNumbers();
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
