<template>
    <div v-if="links.length > 3">
        <ul class="pagination">
            <li v-for="(link, index) in links" :key="index" class="page-item" :class="{ 'active': link.active }">
                <a v-if="!link.url"
                   class="page-link"
                   href="#"
                   v-html="link.label"
                   :class="{ 'disabled': !link.url }"
                />
                <InertiaLink v-else :href="link.url" class="page-link" v-html="link.label"/>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<script>
export default { name: 'Pagination' };
</script>
