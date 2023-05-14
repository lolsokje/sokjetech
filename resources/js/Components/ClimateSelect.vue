<template>
    <div class="mb-3">
        <label class="form-label" for="default_climate">Climate</label>
        <select :value="modelValue"
                id="default_climate"
                class="form-select"
                @change.prevent="updateClimate($event)"
        >
            <option v-for="climate in climates" :key="climate.id" :value="climate.id">{{ climate.name }}</option>
        </select>
    </div>

    <div class="mb-3" v-if="conditions">
        <h5>Condition chances</h5>
        <table class="table table-narrow">
            <thead>
            <tr>
                <th class="text-center" v-for="condition in conditions" :key="condition.id">
                    {{ condition.condition_name }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="smallest-centered" v-for="condition in conditions" :key="condition.id">
                    {{ condition.chance }}%
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts" setup>
import Climate from '@/Interfaces/Climate';
import { ref, Ref, watch } from 'vue';
import Condition from '@/Interfaces/Condition';

interface Props {
    climates: Climate[],
    modelValue: string | null,
}

const props = defineProps<Props>();
const emit = defineEmits([ 'update:modelValue' ]);

const getCondition = (climateId: string | null): Condition[] | null => {
    const climate = props.climates.find((c: Climate) => c.id === climateId);

    return climate ? climate.conditions : null;
};

const updateClimate = (event: Event): void => {
    const climateId: string = event.target.value;
    emit('update:modelValue', climateId);
    setConditions(climateId);
};

const setConditions = (climateId: string | null): void => {
    conditions.value = getCondition(climateId);
};

const conditions: Ref<Condition[] | null> = ref(getCondition(props.modelValue));

watch(props, (value: Props, oldValue: Props) => {
    setConditions(oldValue.modelValue);
});
</script>
