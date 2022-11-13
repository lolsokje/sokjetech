<template>
    <Base>
        <div class="st-card p-4">
            <div class="col-6">
                <slot/>
            </div>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Layouts/Base.vue';</script>

<script>
export default { name: "Tutorial" };
</script>
