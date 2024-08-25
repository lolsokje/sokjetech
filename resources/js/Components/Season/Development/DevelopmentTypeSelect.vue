<template>
    <h4>Types</h4>
    <div v-for="type in types" :key="type">
        <p class="mb-2 mt-3">{{ uppercaseFirstLetter(type.name) }}</p>
        <div class="d-flex gap-2">
            <span v-for="component in type.components"
                  :key="component"
                  class="component-button"
                  :class="{ 'active': developmentStore.selectedType === type.name && developmentStore.selectedComponent === component }"
                  @click.prevent="setComponent(type.name, component)"
                  :data-type="type.name"
            >
                {{ component }}
            </span>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { uppercaseFirstLetter } from '@/Support/String';
import { developmentStore } from '@/Stores/developmentStore';
import { router } from '@inertiajs/vue3';
import { seasonStore } from '@/Stores/seasonStore';
import DevelopmentTypes from '@/Constants/DevelopmentTypes';

const types = DevelopmentTypes;

const setComponent = (type: string, component: string): void => {
    const currentRouteName: string = route().current();

    router.get(route(currentRouteName, { season: seasonStore.season, type, component }));
};
</script>

<style scoped lang="scss">
$primary: var(--primary);

.component-button {
    background-color: $primary;
    border-radius: 0.25rem;
    padding: 0.2rem 0.5rem;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;

    &:hover {
        background-color: var(--base-100);
        outline: 1px solid var(--primary);
    }

    &.active {
        background-color: var(--base-100);
        outline: 2px solid var(--secondary);
    }
}
</style>
