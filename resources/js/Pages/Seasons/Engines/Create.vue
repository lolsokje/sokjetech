<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <form @submit.prevent="form.post(route('seasons.engines.store', [season]))">
        <Errors :errors="form.errors"/>

        <div class="mb-3">
            <label for="base_engine" class="form-label">Base engine</label>
            <select v-model="state.base_engine" id="base_engine" class="form-control" required>
                <option v-for="engine in engines" :key="engine.id" :value="engine.id">{{ engine.name }}</option>
            </select>
        </div>

        <div v-if="state.base_engine">
            <div class="mb-3 form-check form-check-inline">
                <input type="checkbox" class="form-check-input" v-model="state.rebadge" id="rebadge">
                <label class="form-check-label" for="rebadge">Rebadge engine?</label>
                <p>
                    <small>
                        Rebadging an engine allows you to enter the same engine multiple times but under a different
                        name, like Red Bull Racing running "TAG Heuer" engines in the 2017 F1 season, which were
                        rebadged Renault power units.
                    </small>
                </p>
            </div>

            <div v-if="state.rebadge">
                <div class="mb-3">
                    <label for="name" class="form-label">Rebadged engine name</label>
                    <input type="text" v-model="state.name" id="name" class="form-control" required>
                </div>
                <div class="mb-3 form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" v-model="form.individual_rating"
                           id="individual_rating">
                    <label class="form-check-label" for="individual_rating">Individual engine ratings?</label>
                    <p>
                        <small>
                            Individual engine ratings allow dev for rebadged engines to be different from its base
                            engine. Leaving this off means a rebadged engine will use its base engine's ratings
                        </small>
                    </p>
                </div>
            </div>
        </div>

        <button class="btn btn-primary" type="submit" v-if="showSubmitButton">Add engine to season</button>
    </form>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import BackLink from '@/Shared/BackLink';
import Errors from '@/Shared/Errors';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    engines: {
        type: Array,
        required: true,
    },
});

const state = reactive({
    base_engine: null,
    rebadge: false,
    name: '',
});

const form = useForm({
    base_engine_id: null,
    rebadge: state.rebadge,
    individual_rating: false,
    name: '',
});

const showSubmitButton = computed(() => {
    if (form.base_engine_id) {
        return !(form.rebadge && form.name.length < 3);
    }
    return false;
});

watch(state, (newState) => {
    form.base_engine_id = newState.base_engine;
    form.rebadge = newState.rebadge;

    if (newState.rebadge) {
        form.name = newState.name;
    } else {
        const engine = props.engines.find(e => e.id === form.base_engine_id);
        form.name = engine.name;
    }
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
