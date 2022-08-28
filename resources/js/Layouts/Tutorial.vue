<template>
    <Base>
        <div class="bg-dark p-4">
            <div class="col-6">
                <slot/>
            </div>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Layouts/Base';</script>

<script>
export default { name: "Tutorial" };
</script>
