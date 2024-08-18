<template>
    <h2 class="text-center">
        {{ type }} |
        {{ uppercaseFirstLetter(developmentStore.selectedType) }} |
        {{ uppercaseFirstLetter(developmentStore.selectedComponent) }}
    </h2>
</template>

<script lang="ts" setup>
import { uppercaseFirstLetter } from '@/Support/String';
import { developmentStore } from '@/Stores/developmentStore';

type Props = {
    type?: 'Development' | 'History'
};

withDefaults(defineProps<Props>(), {
    type: 'Development',
});
</script>
