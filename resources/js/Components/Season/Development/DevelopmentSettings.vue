<template>
    <h2>Settings</h2>

    <DevelopmentTypeSelect :types="developmentStore.types"/>

    <h4 class="mt-4">Configuration</h4>
    <div class="d-flex justify-content-between gap-3">
        <div>
            <label for="min">Min</label>
            <input type="number" id="min" class="form-control" v-model="developmentStore.min">
        </div>
        <div>
            <label for="max">Max</label>
            <input type="number" id="max" class="form-control" v-model="developmentStore.max">
        </div>
    </div>

    <button class="btn btn-outline-warning mt-3" @click.prevent="applyRanges()">Apply</button>

    <div class="mt-3">
        <div>
            <input type="checkbox"
                   id="hide_inputs"
                   class="form-check-inline"
                   v-model="developmentStore.hide_inputs"
            >
            <label for="hide_inputs" class="form-check-label">Hide inputs</label>
        </div>

        <div>
            <input type="checkbox"
                   id="edit"
                   class="form-check-inline"
                   v-model="developmentStore.edit"
                   :disabled="!developmentStore.completed"
            >
            <label for="edit" class="form-check-label">Edit ratings directly</label>
            <p v-if="!developmentStore.completed">
                <small>
                    Run dev first before editing individual ratings
                </small>
            </p>
        </div>
    </div>

</template>

<script lang="ts" setup>
import { developmentStore } from '@/Stores/developmentStore';
import DevelopmentTypeSelect from '@/Components/Season/Development/DevelopmentTypeSelect.vue';

const applyRanges = (): void => {
    developmentStore.form.entities.forEach(entity => {
        entity.min = developmentStore.min;
        entity.max = developmentStore.max;
    });
};
</script>
