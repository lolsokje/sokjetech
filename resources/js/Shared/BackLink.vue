<template>
    <InertiaLink :href="backTo" class="d-inline-block my-2">&larr; back to {{ label }}</InertiaLink>
</template>

<script setup>
defineProps({
    backTo: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: false,
        default: 'previous page',
    },
});
</script>

<script>
export default { name: 'BackLink' };
</script>
