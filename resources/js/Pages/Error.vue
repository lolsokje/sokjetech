<template>
    <h1>{{ flag }} Flag!</h1>
    <h4>{{ description }}</h4>
    <p class="text-muted">Error code: {{ status }}</p>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ status: Number });

const flag = computed(() => {
    const status = props.status;

    if (status === 404) {
        return 'Red';
    } else if (status >= 500 && status < 600) {
        return 'Yellow';
    }
    return 'Black';
});

const description = computed(() => {
    return {
        403: "Five second penalty, you're not allowed to view the requested page",
        404: "You must have taken a wrong turn, the requested page doesn't exist",
    }[props.status] ?? "Something went wrong, we're checking";
});
</script>

<script>
import ErrorLayout from '@/Layouts/ErrorLayout';

export default { layout: ErrorLayout };
</script>
