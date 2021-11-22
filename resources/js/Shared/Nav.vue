<template>
	<nav class="sidebar" :class="sidebarActive">
		<img v-if="user" :src="user.avatar" alt="" class="rounded-circle mx-auto d-block mt-4">
		<ul v-if="user" class="list-unstyled nav flex-column mt-5">
			<li class="nav-item ps-3">
				<Link :href="route('auth.logout')" method="POST" as="button" class="btn btn-link">Logout</Link>
			</li>
		</ul>
		<ul v-else class="list-unstyled nav flex-column mt-5">
			<li class="nav-item ps-3">
				<a :href="route('auth.redirect')" class="nav-link">Log in</a>
			</li>
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
		user () {
			return this.$page.props.auth.user;
		},
	},
	methods: {
		toggleSidebar () {
			this.visible = !this.visible;
		},
	},
};
</script>
