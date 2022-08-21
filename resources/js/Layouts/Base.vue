<template>
    <div class="min-vh-100 d-flex flex-column justify-content-between">
        <div>
            <Nav/>

            <div id="content" class="px-5 mb-3">
                <Toast/>

                <main class="flex-shrink-0">
                    <slot/>
                </main>
            </div>
        </div>

        <footer class="bg-dark text-center">
            <span id="version" v-if="version"><small>V{{ version }}</small></span>
        </footer>
    </div>
</template>

<script setup>
import Toast from '../Shared/Toast';
import Nav from '@/Shared/Nav';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const version = ref('');

onMounted(async () => {
    const response = await axios.get(route('version'));
    version.value = response.data;
});
</script>

<script>
export default { name: 'Base' };
</script>
