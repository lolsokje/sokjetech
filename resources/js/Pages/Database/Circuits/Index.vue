<template>
    <h3>Global circuits database</h3>

    <input type="text" class="form-control mb-3 w-25" v-model="params.search" placeholder="Search">
    <table class="table">
        <thead>
        <tr>
            <th role="button" @click="sortTable(params, 'name')">
                <span>Name</span>
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="name"/>
            </th>
            <th class="text-center" role="button" @click="sortTable(params, 'country')">
                Country
                <OrderIcon :current-field="params.field" :direction="params.direction" required-field="country"/>
            </th>
            <th>Climate</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="circuit in circuits.data" :key="circuit.id">
            <td class="padded-left">{{ circuit.name }}</td>
            <td class="smallest-centered">
                <CountryFlag :country="circuit.country"/>
            </td>
            <td class="small-centered">{{ circuit.default_climate.short_name }}</td>
            <td class="small-centered">
                <button class="btn btn-link" @click.prevent="showDialog(circuit)">copy</button>
            </td>
        </tr>
        </tbody>
    </table>
    <Pagination :links="links"/>

    <CustomModal id="copyCircuitModal">
        <template #header>
            <h5 class="modal-title">Copy circuit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </template>
        <template #default>
            <h5>Which of the available variations do you want to copy?</h5>

            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th class="text-center">Length</th>
                    <th class="text-center">Base laptime</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="variation in variations" :key="variation.id">
                    <td class="smallest-centered">
                        <input type="checkbox" checked v-model="form.variations" :value="variation.id">
                    </td>
                    <td class="padded-left">{{ variation.name }}</td>
                    <td class="big-centered">{{ variation.length.km }}km/{{ variation.length.m }}m</td>
                    <td class="big-centered">{{ variation.laptime.readable }}</td>
                </tr>
                </tbody>
            </table>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button"
                    class="btn btn-primary"
                    @click.prevent="copy()"
            >
                Copy Circuit
            </button>
        </template>
    </CustomModal>
</template>

<script setup lang="ts">
import { onMounted, reactive, Ref, ref, watch } from 'vue';
import { filter, sortTable } from '@/Composables/useTableFiltering.js';
import OrderIcon from '@/Shared/OrderIcon.vue';
import Pagination from '@/Shared/Pagination.vue';
import CustomModal from '@/Components/Modal.vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import CircuitCollection from '@/Interfaces/Circuit/CircuitCollection';
import Filters from '@/Interfaces/Filters';
import Circuit from '@/Interfaces/Circuit';
import CircuitVariation from '@/Interfaces/Circuit/CircuitVariation';
import CircuitVariationResource from '@/Interfaces/Circuit/CircuitVariationResource';
import { useForm } from '@inertiajs/vue3';

interface Props {
    circuits: CircuitCollection,
    links: object[],
    filters: Filters,
}

interface Form {
    variations: string[],
}

const props = defineProps<Props>();

const form = useForm<Form>({
    variations: [],
});

const params = reactive({
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    search: props.filters.search ?? '',
});

const modal: Ref<Modal | null> = ref(null);
const selectedCircuit: Ref<Circuit | null> = ref(null);
const variations: Ref<CircuitVariation[]> = ref([]);

const copy = (): void => {
    form.post(route('database.circuits.copy', selectedCircuit.value), {
        preserveScroll: true,
        onSuccess: () => modal.value.hide(),
    });
};

const showDialog = (circuit: Circuit): void => {
    selectedCircuit.value = circuit;

    axios.get(route('database.circuits.variations.index', {
        circuit: circuit.name,
    }))
        .then(response => {
            const data: CircuitVariationResource = response.data;
            variations.value = data.data;
            form.variations = variations.value.map(v => v.id);
        });

    modal.value.show();
};

watch(params, () => {
    filter(params, route('database.circuits.index'));
});

onMounted(() => {
    modal.value = new Modal('#copyCircuitModal');
});
</script>

<script lang="ts">
import Database from '@/Layouts/Database.vue';

export default { layout: Database };
</script>
