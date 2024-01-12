<template>
    <h1 :class="{ 'mb-3': !withoutMargin }">
        <template v-if="true">
            <InertiaLink :href="link">{{ linkText }}</InertiaLink>
            <span class="text-muted"> /&nbsp;</span>
        </template>
        <template v-if="true">
            <InertiaLink v-if="labelLink" :href="labelLink">{{ label }}</InertiaLink>
            <span v-else>{{ label }}</span>
        </template>
        <template v-if="append">
            <span class="text-muted"> / </span>
            <span :class="{ 'text-muted': !labelLink }">{{ append }}</span>
        </template>
    </h1>
</template>

<script lang="ts" setup>
interface Props {
    link: string,
    linkText: string,
    label: string,
    labelLink?: string,
    append?: string,
    withoutMargin?: boolean // TODO: check if other pages require this margin, remove if not
}

defineProps<Props>();
</script>
