<template>
    <CustomModal id="filterStintsModal" scrollable>
        <template #header>
            <h2>Search for existing stints</h2>
        </template>
        <template #default>
            <div class="row mb-3">
                <div class="col-2">
                    <label for="min_rng" class="form-label">Min rng</label>
                    <input type="number" id="min_rng" v-model="params.min_rng" class="form-control">
                </div>

                <div class="col-2">
                    <label for="max_rng" class="form-label">Max rng</label>
                    <input type="number" id="max_rng" v-model="params.max_rng" class="form-control">
                </div>

                <div class="col-2">
                    <label for="reliability" class="form-label">Reliability</label>
                    <select v-model="params.reliability" id="reliability" class="form-select">
                        <option value="">Any</option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-2">
                    <label for="use_team_rating" class="form-label">Team rating</label>
                    <select v-model="params.use_team_rating" id="use_team_rating" class="form-select">
                        <option value="">Any</option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-2">
                    <label for="use_driver_rating" class="form-label">Driver rating</label>
                    <select v-model="params.use_driver_rating" id="use_driver_rating" class="form-select">
                        <option value="">Any</option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-2">
                    <label for="use_engine_rating" class="form-label">Engine rating</label>
                    <select v-model="params.use_engine_rating" id="use_engine_rating" class="form-select">
                        <option value="">Any</option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>Min RNG</th>
                    <th>Max RNG</th>
                    <th>DNF Chance</th>
                    <th>Team rating</th>
                    <th>Driver rating</th>
                    <th>Engine rating</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="stint in stints" :key="stint.id" class="text-center">
                    <td>{{ stint.min_rng }}</td>
                    <td>{{ stint.max_rng }}</td>
                    <td>{{ stint.reliability }}</td>
                    <td>{{ stint.use_team_rating }}</td>
                    <td>{{ stint.use_driver_rating }}</td>
                    <td>{{ stint.use_engine_rating }}</td>
                    <td class="small-centered">
                        <button class="btn btn-link" @click.prevent="selectStint(stint.id)">
                            add
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
    </CustomModal>
</template>

<script setup>
import CustomModal from '@/Components/Modal';
import { onMounted, reactive, ref, watch } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';
import { getRequestParams } from '@/Composables/useTableFiltering';

const modal = ref(null);
const stints = ref([]);

const emit = defineEmits([ 'selected' ]);

const params = reactive({
    min_rng: null,
    max_rng: null,
    reliability: '',
    use_team_rating: '',
    use_driver_rating: '',
    use_engine_rating: '',
});

const showDialog = () => {
    modal.value.show();
};

const selectStint = (id) => {
    const stint = stints.value.find(s => s.id === id);

    emit('selected', {
        min_rng: stint.min_rng,
        max_rng: stint.max_rng,
        reliability: stint.reliability,
        use_team_rating: stint.use_team_rating,
        use_driver_rating: stint.use_driver_rating,
        use_engine_rating: stint.use_engine_rating,
    });

    modal.value.hide();
    fetchStints();
    resetFilters();
};

const fetchStints = async () => {
    const response = await axios.get(route('stints'));
    stints.value = await response.data;
};

const resetFilters = () => {
    params.min_rng = null;
    params.max_rng = null;
    params.reliability = '';
    params.use_team_rating = '';
    params.use_driver_rating = '';
    params.use_engine_rating = '';
};

onMounted(async () => {
    modal.value = new Modal('#filterStintsModal');

    await fetchStints();
});

watch(params, async () => {
    const requestParams = getRequestParams(params);
    const queryString = Object.keys(requestParams).map(key => `${key}=${requestParams[key]}`).join('&');
    const response = await axios.get(`${route('stints')}?${queryString}`);
    stints.value = response.data;
});

defineExpose({
    showDialog,
});
</script>

<script>
export default { name: "StintFilterModal" };
</script>
