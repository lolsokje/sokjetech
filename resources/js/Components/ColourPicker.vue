<template>
    <Chrome v-model="colour"/>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Chrome } from '@ckpack/vue-color';

const props = defineProps({
    modelValue: String,
});

const emit = defineEmits([ 'update:modelValue' ]);
const colour = ref(props.modelValue);

watch(colour, () => {
    emit('update:modelValue', colour.value.hex);
});
</script>

<script>
export default { name: "ColourPicker" };
</script>
