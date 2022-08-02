<template>
    <button v-if="!isFirefox && hasClipboard" class="btn btn-secondary" @click.prevent="copyScreenshot">Screenshot
    </button>
    <p v-if="isFirefox">
        Due to limitations in Firefox's API, programmatically copying screenshots isn't supported in this browser
    </p>
    <p v-else-if="!hasClipboard">
        The clipboard API is currently unavailable, programmatically copying screenshots is not possible.
    </p>
</template>

<script setup>
import { getScreenshotFromHTML } from '@/Utilities/CopyCanvasImage';
import { computed } from 'vue';

const userAgent = navigator.userAgent;
const isFirefox = userAgent.match(/firefox|fxios/i);
// clipboard won't be available when using HTTP
const hasClipboard = computed(() => navigator.clipboard !== undefined);

async function copyScreenshot () {
    await getScreenshotFromHTML();
}
</script>

<script>
export default {name: 'CopyScreenshotButton'};
</script>
