<template>
	<div class="wrapper d-flex">
		<Nav :class="sidebarActive" />

		<div id="content" class="p-5">
			<Breadcrumbs @toggleVisible="toggleSidebar" />

			<div class="mt-5 bg-dark p-4">
				<slot />
			</div>
		</div>
	</div>
</template>

<script>
import Nav from './Nav';
import Breadcrumbs from './Breadcrumbs';

export default {
	components: { Breadcrumbs, Nav },
	data () {
		return {
			sidebarActive: '',
		};
	},
	methods: {
		toggleSidebar (visible) {
			this.sidebarActive = visible ? '' : 'inactive';
		},
	},
};
</script>
