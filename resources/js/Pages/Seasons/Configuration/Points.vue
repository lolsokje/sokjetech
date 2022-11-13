<template>
    <h3>Points configuration</h3>

    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <form @submit.prevent="form.post(route('seasons.configuration.points.store', [season]))">
        <Errors v-if="form.errors" :errors="form.errors"/>

        <div class="alert alert-warning" v-if="hasSeasonStarted()">
            The season has started, therefore the points configuration can no longer be modified.
        </div>

        <div class="mb-3 w-25">
            <label for="amount_of_point_scorers" class="form-label">Amount of point scorers</label>
            <input type="number" id="amount_of_point_scorers" v-model="amountOfPointScorers" class="form-control"
                   @change="setPointScorerInputs" required :disabled="hasSeasonStarted()">
        </div>

        <div class="row">
            <div class="col-6">
                <h3>Points per position</h3>
                <div class="col-6 mb-3 input-group" v-for="(point, key) in form.points" :key="key">
                    <span class="input-group-text">{{ point.position }}</span>
                    <input type="text" class="form-control" v-model="point.points" :disabled="hasSeasonStarted()">
                </div>
            </div>

            <div class="col-6">
                <h3>Extra points</h3>
                <div class="row mb-3">
                    <div>
                        <input type="checkbox" class="form-check-input me-1" v-model="form.fastest_lap_point_awarded"
                               id="point_for_fastest_lap" :disabled="hasSeasonStarted()">
                        <label for="point_for_fastest_lap" class="form-check-label">Point for fastest lap</label>
                    </div>

                    <div v-if="form.fastest_lap_point_awarded">
                        <div class="my-3">
                            <label for="fastest_lap_points" class="form-label">Fastest lap points</label>
                            <input type="number" class="form-control" v-model="form.fastest_lap_point_amount"
                                   :required="form.fastest_lap_point_awarded" :disabled="hasSeasonStarted()">
                        </div>

                        <div class="my-3">
                            <label for="fastestLapDetermination" class="form-label">Fastest lap determination</label>
                            <select id="fastestLapDetermination" class="form-control"
                                    v-model="form.fastest_lap_determination" :required="form.fastest_lap_point_awarded"
                                    :disabled="hasSeasonStarted()">
                                <option value="">Pick an option</option>
                                <option :value="FastestLapDetermination.BEST_LAST_STINT">Best last stint RNG</option>
                                <option :value="FastestLapDetermination.SEPARATE_STINT">Separate stint</option>
                            </select>
                        </div>

                        <div class="row" v-if="fastestLapIsSeparateStint">
                            <div class="col-6">
                                <label for="fastest_lap_min_rng" class="form-label">Fastest lap min RNG</label>
                                <input type="number" class="form-control" v-model="form.fastest_lap_min_rng"
                                       :required="fastestLapIsSeparateStint" :disabled="hasSeasonStarted()">
                            </div>

                            <div class="col-6">
                                <label for="fastest_lap_max_rng" class="form-label">Fastest lap max RNG</label>
                                <input type="number" class="form-control" v-model="form.fastest_lap_max_rng"
                                       :required="fastestLapIsSeparateStint" :disabled="hasSeasonStarted()">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div>
                        <input type="checkbox" class="form-check-input me-1" v-model="form.pole_position_point_awarded"
                               id="point_for_pole_position" :disabled="hasSeasonStarted()">
                        <label for="point_for_pole_position" class="form-check-label">Point for pole position</label>
                    </div>

                    <div v-if="form.pole_position_point_awarded">
                        <div class="my-3">
                            <label for="amount_of_points_for_pole" class="form-label">Amount of points for pole
                                position</label>
                            <input type="number" class="form-control" v-model="form.pole_position_point_amount"
                                   :required="form.pole_position_point_awarded" :disabled="hasSeasonStarted()">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" v-if="!hasSeasonStarted()">Save</button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { computed, onMounted, ref } from 'vue';
import FastestLapDetermination from '@/Enums/FastestLapDetermination';
import Errors from '@/Shared/Errors.vue';
import BackLink from '@/Shared/BackLink.vue';

const props = defineProps({
    season: {
        type: Object,
        required: false,
    },
    points: {
        type: Array,
        required: false,
    },
});

const amountOfPointScorers = ref(props.points.length ? props.points.length : 10);
const fastestLapIsSeparateStint = computed(() => form.fastest_lap_determination === FastestLapDetermination.SEPARATE_STINT);

const form = useForm({
    points: props?.points,
    pole_position_point_awarded: false,
    fastest_lap_point_awarded: false,
    pole_position_point_amount: 1,
    fastest_lap_point_amount: 1,
    fastest_lap_determination: '',
    fastest_lap_min_rng: 0,
    fastest_lap_max_rng: 0,
});

onMounted(() => {
    if (!form.points.length) {
        let remainingPoints = amountOfPointScorers.value;
        for (let position = 1; position <= amountOfPointScorers.value; position++) {
            addToPointsArray(position, remainingPoints);
            remainingPoints--;
        }
    }

    // fill the form's point system with an existing point system, if present
    Object.assign(form, props.season?.point_system);
});

const setPointScorerInputs = () => {
    const amount = amountOfPointScorers.value;

    if (amount === form.points.length) {
        return;
    }

    if (amount > form.points.length) {
        for (let i = form.points.length + 1; i <= amount; i++) {
            addToPointsArray(i, 0);
        }
    } else {
        form.points = form.points.slice(0, amount);
    }
};

const addToPointsArray = (position, points) => {
    form.points.push({ position, points });
};

const hasSeasonStarted = () => {
    return props.season.started;
};
</script>

<script>
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
