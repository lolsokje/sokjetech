<template>
    <div class="modal" tabindex="-1" :id="id">
        <div class="modal-dialog modal-dialog-centered modal-xl" :class="{ 'modal-dialog-scrollable': scrollable }">
            <div class="modal-content">
                <div class="modal-header">
                    <slot name="header"/>
                </div>
                <div class="modal-body">
                    <slot name="default"/>
                </div>
                <div class="modal-footer">
                    <slot name="footer"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    scrollable: Boolean,
});
</script>

<script>
export default { name: "CustomModal" };
</script>
