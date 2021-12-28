<template>
	<nav class="sidebar">
		<img v-if="user" :src="user.avatar" alt="" class="rounded-circle mx-auto d-block mt-5">
		<ul class="list-unstyled nav flex-column">
			<li class="nav-item ps-3 mt-5">
				<InertiaLink :href="route('index')" class="nav-link">
					<fa class="me-1" icon="home"></fa>
					Home
				</InertiaLink>
			</li>
			<CollapseMenu v-if="user" :items="state.circuitNavItems" icon="road" label="Circuits"/>
			<CollapseMenu :items="state.universeNavItems" icon="globe" label="Universes"/>
			<li v-if="user" class="nav-item ps-3 mt-3">
				<InertiaLink :href="route('auth.logout')" as="button" class="btn btn-link nav-link" method="POST">
					<fa class="me-1" icon="sign-out-alt"></fa>
					Logout
				</InertiaLink>
			</li>
			<li v-if="!user" class="nav-item ps-3 mt-3">
				<a :href="route('auth.redirect')" class="nav-link">
					<fa class="me-1" icon="sign-in-alt"></fa>
					Log in</a>
			</li>
		</ul>
	</nav>
</template>
<script setup>
import CollapseMenu from './CollapseMenu';
import { computed, reactive } from 'vue';
import MenuItem from '../Utilities/MenuItem';
import { usePage } from '@inertiajs/inertia-vue3';

const user = computed(() => usePage().props.value.auth.user);

const state = reactive({
	circuitNavItems: [
		new MenuItem(route('circuits.index'), 'View', 'th-list'),
		new MenuItem(route('circuits.create'), 'Create', 'plus'),
	],
	universeNavItems: [
		new MenuItem(route('universes.index'), 'View', 'th-list'),
		new MenuItem(route('universes.create'), 'Create', 'plus', true),
	],
});
</script>
