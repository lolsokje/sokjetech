<template>
  <!--  <div v-if="notice" class="alert alert-success fade show position-absolute top-0 start-50 translate-middle-x mt-4">-->
  <!--    {{ notice }}-->
  <!--  </div>-->
  <div class="position-fixed top-0 end-0 p-3 text-white" style="z-index: 11" v-show="notice">
    <div class="toast p-2 bg-success" role="alert">
      <div class="toast-body bg-success">
        {{ notice }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { Toast } from 'bootstrap';

onMounted(() => {
  const toastElList = [].slice.call(document.querySelectorAll('.toast'));
  const toasts = toastElList.map(function (toastEl) {
    return new Toast(toastEl, { autohide: false });
  });
  toasts.forEach(toast => toast.show());
});
const notice = computed(() => usePage().props.value.flash.notice);
</script>

<style scoped>
.toast-body {
  font-size: 1.3rem;
}
</style>