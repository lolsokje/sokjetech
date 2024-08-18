<template>
    <div class="d-flex gap-2">
        <button class="btn btn-primary"
                :disabled="developmentStore.completed"
                @click.prevent="runDevelopment()"
        >
            Run
        </button>
        <button class="btn btn-success"
                :disabled="!developmentStore.completed || developmentStore.form.processing"
                @click.prevent="saveDevelopment()"
        >
            Save
        </button>
    </div>
</template>

<script lang="ts" setup>
import { developmentStore } from '@/Stores/developmentStore';
import { seasonStore } from '@/Stores/seasonStore';
import { getRoll } from '@/Composables/useRandom';

const runDevelopment = (): void => {
    developmentStore.form.entities.forEach(entity => {
        entity.rng = getRoll(entity.min, entity.max);
        entity.new = entity.current + entity.rng;
    });

    if (developmentStore.isEngine) {
        const rebadgedEnginesWithIndividualRating = developmentStore.form.entities.filter(entity => entity.extra.rebadged && ! entity.extra.individual_rating);

        rebadgedEnginesWithIndividualRating.forEach(engine => {
            const parentEngine = developmentStore.form.entities.find(e => e.extra.base_engine_id === engine.extra.base_engine_id && ! e.extra.rebadged);

            if (! parentEngine) {
                return;
            }

            engine.rng = parentEngine.rng;
            engine.new = parentEngine.new;
        });
    }

    developmentStore.completed = true;
};

const saveDevelopment = (): void => {
    developmentStore.form.post(route('seasons.development.store', {
        season: seasonStore.season,
        type: developmentStore.selectedType,
        component: developmentStore.selectedComponent,
    }));
};
</script>
