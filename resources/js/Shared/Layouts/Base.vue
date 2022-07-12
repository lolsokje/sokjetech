<template>
    <div class="wrapper d-flex">
        <Nav :class="state.sidebarActive"/>

        <div id="content" class="p-5">
            <Breadcrumbs @toggleVisible="toggleSidebar"/>
            <Toast/>

            <main>
                <slot/>
            </main>
        </div>
    </div>
</template>

<script setup>
import Nav from '../Nav';
import Breadcrumbs from '../Breadcrumbs';
import { reactive } from 'vue';
import Toast from '../Toast';

const state = reactive({
    sidebarActive: localStorage.getItem('sidebar_visible') === 'false' ? 'inactive' : '',
});

async function toggleSidebar (visible) {
    state.sidebarActive = visible ? '' : 'inactive';
}
</script>

<script>
export default { name: 'Base' };
</script>
