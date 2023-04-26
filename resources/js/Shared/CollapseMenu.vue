<template>
    <li :class="sidebarStatus" class="nav-item has-sub ps-3">
        <a class="nav-link" href="#" @click="toggleMenu">
            <fa v-if="icon" :icon="icon" class="me-1"/>
            {{ this.label }}
            <fa :icon="caretIcon" class="me-2 mt-1" pull="right"></fa>
        </a>
        <ul class="collapsable list-unstyled ps-3">
            <li v-for="(item, index) in items" v-bind:key="index" class="nav-item">
                <InertiaLink v-if="item.authRequired && user || !item.authRequired" :href="item.url" class="nav-link">
                    <fa v-if="item.icon" :icon="item.icon" class="me-1"/>
                    {{ item.label }}
                </InertiaLink>
            </li>
        </ul>
    </li>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    icon: { type: String, required: false },
    label: { type: String, required: true },
    items: { type: Array, required: true },
});

const state = reactive({
    open: false,
});

function toggleMenu () {
    state.open = ! state.open;
}

const sidebarStatus = computed(() => state.open ? 'open' : '');
const caretIcon = computed(() => state.open ? 'caret-down' : 'caret-right');
const user = computed(() => usePage().props.auth.user);
</script>
