<template>
	<nav class="navbar navbar-expand lg navbar-dark bg-dark rounded">
		<div class="container-fluid py-1 me-2">
			<fa :icon="sidebarIcon" class="ms-auto fa-2x" role="button" @click="toggleSidebar" />
		</div>
	</nav>
</template>

<script>
export default {
	name: 'Breadcrumbs',
	data () {
		return {
			visible: true,
		};
	},
	computed: {
		sidebarIcon () {
			return this.visible ? 'times' : 'bars';
		},
		sidebarActive () {
			return this.visible ? '' : 'inactive';
		},
	},
	methods: {
		toggleSidebar () {
			this.visible = !this.visible;
			this.$emit('toggleVisible', this.visible);
		},
	},
};
</script>
