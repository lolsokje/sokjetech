<template>
	<form>
		<div class="mb-3">
			<label for="format" class="form-label">Qualifying format</label>
			<select id="format" v-model="selectedFormat" class="form-control" required>
				<option v-for="(format, key) in formats" :key="key" :value="key">{{ format }}</option>
			</select>
		</div>

		<ThreeSessionElimination v-if="selectedFormat === 0"/>
		<SingleSession v-if="selectedFormat === 1"/>

	</form>
</template>

<script setup>
import { ref } from 'vue';
import ThreeSessionElimination from '@/Shared/QualifyingFormats/ThreeSessionElimination';
import SingleSession from '@/Shared/QualifyingFormats/SingleSession';

const props = defineProps({
	season: {
		type: Object,
		required: true,
	},
	formats: {
		type: Array,
		required: true,
	},
});

const selectedFormat = ref(null);
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
