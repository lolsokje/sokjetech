<template>
    <td class="smallest-centered driver-number-wrapper">
        <span class="driver-number" :style="styleString">{{ number }}</span>
    </td>
</template>

<script lang="ts" setup>
interface Props {
    styleString: string,
    number: number,
}

defineProps<Props>();
</script>
