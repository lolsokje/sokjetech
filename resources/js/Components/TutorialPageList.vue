<template>
    <p v-if="title" class="mb-0">{{ title }}</p>
    <ul class="list-unstyled">
        <li v-for="(pages, index) in pages" :key="index">
            <InertiaLink :href="pages.link">{{ pages.label }}</InertiaLink>
        </li>
    </ul>
</template>

<script setup>
defineProps({
    pages: Array,
    title: String,
});
</script>

<script>
export default { name: "TutorialPageList" };
</script>
