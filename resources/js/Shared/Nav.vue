<template>
	<nav class="sidebar">
		<img v-if="user" :src="user.avatar" alt="" class="rounded-circle mx-auto d-block mt-5">
		<ul class="list-unstyled nav flex-column">
			<li class="nav-item ps-3 mt-5">
				<Link :href="route('index')" class="nav-link">
					<fa class="me-1" icon="home"></fa>
					Home
				</Link>
			</li>
			<template v-if="user">
				<CollapseMenu :items="state.circuitNavItems" icon="road" label="Circuits" />
				<li class="nav-item ps-3">
					<Link :href="route('auth.logout')" as="button" class="btn btn-link" method="POST">Logout</Link>
				</li>
			</template>
			<li v-else class="nav-item ps-3">
				<a :href="route('auth.redirect')" class="nav-link">Log in</a>
			</li>
		</ul>
	</nav>
</template>
<script setup>
import CollapseMenu from './CollapseMenu';
import { computed, reactive } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

const state = reactive({
	circuitNavItems: [
		{ url: route('circuits.index'), label: 'View', icon: 'th-list' },
		{ url: route('circuits.create'), label: 'Create', icon: 'plus' },
	],
});

const user = computed(() => usePage().props.value.auth.user);
</script>
