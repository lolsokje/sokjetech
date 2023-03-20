<template>
    <div class="position-fixed top-0 end-0 p-3 text-white" style="z-index: 11" v-show="notice">
        <div class="toast p-2 bg-success" role="alert">
            <div class="toast-body bg-success">
                {{ notice }}
            </div>
        </div>
    </div>
    <div class="position-fixed top-0 end-0 p-3 text-white" style="z-index: 11" v-show="error">
        <div class="toast p-2 bg-danger" role="alert">
            <div class="toast-body bg-danger">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Toast } from 'bootstrap';

onMounted(() => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toasts = toastElList.map(function (toastEl) {
        return new Toast(toastEl, { autohide: false });
    });
    toasts.forEach(toast => toast.show());
});
const notice = computed(() => usePage().props.flash.notice);
const error = computed(() => usePage().props.flash.error);
</script>

<style scoped>
.toast-body {
    font-size: 1.3rem;
}
</style>
