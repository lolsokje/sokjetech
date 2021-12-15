<template>
	<div class="wrapper d-flex">
		<Nav :class="state.sidebarActive"/>

		<div id="content" class="p-5">
			<Breadcrumbs @toggleVisible="toggleSidebar"/>

			<main>
				<div class="wrapper d-flex">
					<div class="w-100 mt-5 bg-dark p-4">
						<h1>{{ series.name }}</h1>
						<slot/>
					</div>
					<div class="w-25 ms-5 mt-5 bg-dark p-4">
						<ul class="nav flex-column">
							<li class="nav-item">
								<a class="nav-link" href="#">Engines</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">Teams</a>
							</li>
						</ul>
					</div>
				</div>
			</main>
		</div>
	</div>
</template>

<script setup>
import Nav from '../Nav';
import Breadcrumbs from '../Breadcrumbs';
import { reactive } from 'vue';

const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
	series: {
		type: Object,
		required: true,
	},
});

const state = reactive({
	sidebarActive: '',
});

async function toggleSidebar (visible) {
	state.sidebarActive = visible ? '' : 'inactive';
}
</script>

<script>
export default { name: 'SeriesLayout' };
</script>
