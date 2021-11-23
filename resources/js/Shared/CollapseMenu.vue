<template>
	<li class="nav-item has-sub ps-3" :class="sidebarStatus">
		<a class="nav-link" href="#" @click="toggleMenu">
			<fa v-if="icon" :icon="icon" class="me-1" />
			{{ this.label }}
			<fa :icon="caretIcon" pull="right" class="me-2 mt-1"></fa>
		</a>
		<ul class="collapsable list-unstyled ps-3">
			<li class="nav-item" v-for="(item, index) in items" v-bind:key="index">
				<Link class="nav-link" :href="item.url">
					<fa v-if="item.icon" :icon="item.icon" class="me-1" />
					{{ item.label }}
				</Link>
			</li>
		</ul>
	</li>
</template>

<script setup>
import { computed, reactive } from 'vue';

const props = defineProps({
	icon: { type: String, required: false },
	label: { type: String, required: true },
	items: { type: Array, required: true },
});

const state = reactive({
	open: false,
});

function toggleMenu () {
	state.open = !state.open;
}

const sidebarStatus = computed(() => state.open ? 'open' : '');
const caretIcon = computed(() => state.open ? 'caret-down' : 'caret-right');
</script>
