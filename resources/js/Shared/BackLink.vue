<template>
    <div class="my-3">
        <InertiaLink :href="backTo">&larr; back to {{ label }}</InertiaLink>
    </div>
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
