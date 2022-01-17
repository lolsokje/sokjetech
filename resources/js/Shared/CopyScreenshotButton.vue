<template>
	<button v-if="!isFirefox" class="btn btn-secondary" @click.prevent="copyScreenshot">Screenshot</button>
	<p v-else>
		Due to limitations in Firefox's API, programmatically copying screenshots isn't supported in this browser
	</p>
</template>

<script setup>
import { getScreenshotFromHTML } from '../Utilities/CopyCanvasImage';

const userAgent = navigator.userAgent;
const isFirefox = userAgent.match(/firefox|fxios/i);

async function copyScreenshot () {
	await getScreenshotFromHTML();
}
</script>

<script>
export default { name: 'CopyScreenshotButton' };
</script>
