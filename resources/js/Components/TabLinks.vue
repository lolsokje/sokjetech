<template>
    <ul class="nav pills">
        <template v-for="(link, key) in links" :key="key">
            <li class="nav-item" v-if="link.show">
                <template v-if="link.hasChildren()">
                    <a class="nav-link dropdown-toggle"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button"
                       aria-expanded="false"
                    >
                        {{ link.label }}
                    </a>
                    <ul class="dropdown-menu">
                        <li v-for="(child, index) in link.getChildren()" :key="index">
                            <InertiaLink :href="child.getRoute()" class="dropdown-item">{{ child.label }}</InertiaLink>
                        </li>
                    </ul>
                </template>
                <template v-else>
                    <InertiaLink :href="link.getRoute()" class="nav-link" :class="link.isActive() ? 'active' : ''">
                        {{ link.label }}
                    </InertiaLink>
                </template>
            </li>
        </template>
    </ul>
</template>

<script setup>
defineProps({
    links: Array,
});
</script>

<script>
export default { name: "TabLinks" };
</script>
