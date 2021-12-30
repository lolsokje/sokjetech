<template>
	<div class="col-6 mt-4">
		<label class="form-label">Preview</label>
		<div :style="style" class="team-name-preview">
			{{ name }}
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	name: {
		type: String,
		required: true,
	},
	backgroundColour: {
		type: String,
		required: true,
	},
	textColour: {
		type: String,
		required: true,
	},
});

const style = computed(() => {
	return {
		backgroundColor: props.backgroundColour,
		color: props.textColour,
	};
});
</script>

<script>
export default { name: 'TeamNamePreview' };
</script>
