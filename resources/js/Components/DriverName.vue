<template>
    <span class="text-gray">{{ firstName }}</span>&nbsp;
    <span class="text-uppercase fw-bolder">{{ lastName }}</span>
</template>

<script lang="ts" setup>
interface Props {
    firstName: string,
    lastName: string,
}

defineProps<Props>();
</script>
