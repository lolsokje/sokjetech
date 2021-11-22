<template>
	<nav class="sidebar" :class="sidebarActive">
		<ul class="list-unstyled nav flex-column mt-5">
			<li class="nav-item ps-3">
				<a :href="route('auth.redirect')" class="nav-link">Log in</a>
			</li>
			<!--			<li class="nav-item"><a href="#" class="nav-link ps-3" data-bs-target="#testMenu" data-bs-toggle="collapse">Test</a>-->
			<!--			</li>-->
			<!--			<ul class="list-unstyled collapse" id="testMenu">-->
			<!--				<li class="nav-item"><a href="#" class="nav-link ps-4">Nog een test</a></li>-->
			<!--			</ul>-->
		</ul>
	</nav>

	<fa @click="toggleSidebar" :icon="sidebarIcon" class="mt-3 ms-3 fa-2x" role="button" />
</template>
<script>
export default {
	name: 'Nav',
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
		},
	},
};
</script>
