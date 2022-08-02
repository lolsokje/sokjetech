<template>
    <td class="colour-accent" :style="`background-color: ${backgroundColour}`"></td>
</template>

<script setup>
defineProps({ backgroundColour: String });
</script>

<script>
export default { name: "BackgroundColourCell" };
</script>
