<template>
    <p v-if="title" class="mb-0">{{ title }}</p>
    <ul :class="classes">
        <li v-for="(page, index) in pages" :key="index">
            <InertiaLink :href="page.getLink()">{{ page.label }}</InertiaLink>
            <template v-if="page.hasChildren()">
                <TutorialPageList :pages="page.children" classes="ps-4"/>
            </template>
        </li>
    </ul>
</template>

<script setup>
defineProps({
    pages: Array,
    title: String,
    classes: {
        type: String,
        required: false,
        default: 'list-unstyled',
    },
});
</script>

<script>
export default { name: "TutorialPageList" };
</script>
