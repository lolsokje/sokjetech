<template>
    <h3>Reliability configuration</h3>

    <BackLink :backTo="route('seasons.races.index', [season])" label="season overview"/>

    <form @submit.prevent="form.post(route('seasons.configuration.reliability.store', [season]))">
        <Errors v-if="form.errors" :errors="form.errors"/>

        <div class="alert alert-warning" v-if="hasSeasonStarted()">
            The season has started, therefore the points configuration can no longer be modified.
        </div>

        <div class="row my-3">
            <div class="col-3">
                <label for="min_rng" class="form-label">Min RNG</label>
                <input type="number"
                       class="form-control"
                       v-model="form.min_rng"
                       required
                       :disabled="hasSeasonStarted()"
                >
            </div>
            <div class="col-3">
                <label for="max_rng" class="form-label">Max RNG</label>
                <input type="number"
                       class="form-control"
                       v-model="form.max_rng"
                       required
                       :disabled="hasSeasonStarted()"
                >
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-4">
                <label for="engine" class="form-label">Engine reliability reasons (one per line)</label>
                <textarea v-model="form.reasons.engine"
                          id="engine"
                          rows="10"
                          class="form-control"
                          :disabled="hasSeasonStarted()"
                ></textarea>
            </div>
            <div class="col-4">
                <label for="team" class="form-label">Team reliability reasons (one per line)</label>
                <textarea v-model="form.reasons.team"
                          id="team"
                          rows="10"
                          class="form-control"
                          :disabled="hasSeasonStarted()"
                ></textarea>
            </div>
            <div class="col-4">
                <label for="driver" class="form-label">Driver reliability reasons (one per line)</label>
                <textarea v-model="form.reasons.driver"
                          id="driver"
                          rows="10"
                          class="form-control"
                          :disabled="hasSeasonStarted()"
                ></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" v-if="!hasSeasonStarted()" :disabled="!validForm">Save</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import BackLink from '@/Shared/BackLink.vue';
import Errors from '@/Shared/Errors.vue';
import { computed, onMounted } from 'vue';
import SeasonInterface from '@/Interfaces/Season';

interface ReliabilityConfiguration {
    min_rng: number,
    max_rng: number,
}

interface ReliabilityReasons {
    drivers: string[],
    engine: string[],
    team: string[],
}

interface Props {
    season: SeasonInterface,
    configuration: ReliabilityConfiguration | null,
    reasons: ReliabilityReasons,
}

const props = defineProps<Props>();

const reasons = props.reasons;

const form = useForm({
    min_rng: props.configuration?.min_rng ?? 0,
    max_rng: props.configuration?.max_rng ?? 0,
    reasons: {
        engine: "Power Unit",
        team: "Suspension",
        driver: "Crash",
    },
});

const hasSeasonStarted = (): boolean => {
    return props.season.started;
};

const validForm = computed(() => {
    return form.max_rng > form.min_rng && ! invalidReasons.value;
});

const invalidReasons = computed(() => {
    return Object.values(form.reasons).some(reasons => reasons.trim() === '');
});

onMounted(() => {
    Object.keys(reasons).forEach((key: string) => {
        form.reasons[key] = reasons[key].join('\n');
    });
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
