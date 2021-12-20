<template>
	<div v-if="notice" class="alert alert-success fade show position-absolute top-0 start-50 translate-middle-x mt-4">
		{{ notice }}
	</div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

const notice = computed(() => usePage().props.value.notice);
</script>
