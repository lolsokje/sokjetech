<template>
    <Breadcrumb :link="route('index')" linkText="Home" label="Climates"/>

    <table class="table table-narrow">
        <thead>
        <tr>
            <th></th>
            <th class="text-center" v-for="climate in climates">{{ climate.name }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(condition, key) in conditions">
            <td class="bg-accent-odd medium-centered pe-3" style="text-align: right">{{ condition }}</td>
            <td class="medium-centered" v-for="climate in climates">
                {{ getConditionChanceForClimate(climate, key) }}%
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script lang="ts" setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface WeatherCondition {
    [key: number]: string,
}

interface Condition {
    condition: number,
    chance: number,
}

interface Climate {
    name: string,
    conditions: Condition[],
}

interface Props {
    climates: Climate[],
    conditions: WeatherCondition[]
}

defineProps<Props>();

const getConditionChanceForClimate = (climate: Climate, condition: number): number => {
    const matchedCondition = climate.conditions.find((c: Condition) => c.condition === condition);

    return matchedCondition ? matchedCondition.chance : 0;
};
</script>
