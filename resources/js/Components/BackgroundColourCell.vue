<template>
    <td class="colour-accent-wrapper">
        <span class="colour-accent" :style="`background-color: ${backgroundColour}`"></span>
    </td>
</template>

<script setup>
defineProps({ backgroundColour: String });
</script>

<script>
export default { name: "BackgroundColourCell" };
</script>
