<template>
	<nav class="sidebar">
		<img v-if="user" :src="user.avatar" alt="" class="rounded-circle mx-auto d-block my-5">
		<ul v-if="user" class="list-unstyled nav flex-column">
			<CollapseMenu :items="mainNavItems" icon="road" label="Circuits" />
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
import CollapseMenu from './CollapseMenu';

export default {
	name: 'Nav',
	components: { CollapseMenu },
	data () {
		return {
			mainNavItems: [
				{ url: route('circuits.index'), label: 'View', icon: 'th-list' },
			],
		};
	},
	computed: {
		user () {
			return this.$page.props.auth.user;
		},
	},
	methods: {
		toggleMenu (event) {
			const parent = event.target.parentNode;
			parent.classList.contains('open') ? parent.classList.remove('open') : parent.classList.add('open');
		},
	},
};
</script>
