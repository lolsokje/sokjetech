<template>
	<nav class="sidebar">
		<img v-if="user" :src="user.avatar" alt="" class="rounded-circle mx-auto d-block my-5">
		<ul v-if="user" class="list-unstyled nav flex-column">
			<li class="nav-item ps-3">
				<Link :href="route('auth.logout')" method="POST" as="button" class="btn btn-link">Logout</Link>
			</li>
		</ul>
		<ul v-else class="list-unstyled nav flex-column">
			<li class="nav-item ps-3">
				<a :href="route('auth.redirect')" class="nav-link">Log in</a>
			</li>
		</ul>
	</nav>
</template>
<script>
export default {
	name: 'Nav',
	computed: {
		user () {
			return this.$page.props.auth.user;
		},
	},
};
</script>
