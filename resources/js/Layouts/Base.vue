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
    </div>
</template>

<script setup>
import Toast from '../Shared/Toast';
import Nav from '@/Shared/Nav';</script>

<script>
export default { name: 'Base' };
</script>
