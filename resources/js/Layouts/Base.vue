<template>
    <Nav/>

    <div id="content" class="px-5 mb-3">
        <Toast/>

        <main>
            <slot/>
        </main>
    </div>
</template>

<script setup>
import Toast from '../Shared/Toast';
import Nav from '@/Shared/Nav';</script>

<script>
export default { name: 'Base' };
</script>
