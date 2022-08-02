<template>
    <nav class="navbar navbar-expand lg navbar-dark bg-dark rounded">
        <div class="container-fluid py-1 me-2">
            <fa :icon="sidebarIcon" class="ms-auto fa-2x" role="button" @click="toggleSidebar"/>
        </div>
    </nav>
</template>

<script setup>
import { computed, reactive } from 'vue';

const state = reactive({
    visible: localStorage.getItem('sidebar_visible') !== 'false',
});

const emit = defineEmits([ 'toggleVisible' ]);

function toggleSidebar () {
    state.visible = !state.visible;
    localStorage.setItem('sidebar_visible', state.visible);
    emit('toggleVisible', state.visible);
}

const sidebarIcon = computed(() => state.visible ? 'times' : 'bars');
</script>
