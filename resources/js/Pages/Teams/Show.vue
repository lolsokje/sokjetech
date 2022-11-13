<template>

</template>

<script setup>
defineProps({
    universe: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import Universe from '@/Layouts/Universe.vue';

export default { layout: Universe };
</script>
