<template>
	<div class="wrapper d-flex">
		<Nav />

		<div id="content" class="p-5">
			<slot />
		</div>
	</div>
</template>

<script>
import Nav from './Nav';

export default {
	components: { Nav },
};
</script>
