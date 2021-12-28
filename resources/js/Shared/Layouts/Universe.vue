<template>
	<Base>
		<div class="wrapper d-flex">
			<div class="w-100 mt-5 bg-dark p-4">
				<h1>{{ universe.name }}</h1>
				<slot/>
			</div>
			<div class="w-25 ms-5 mt-5 bg-dark p-4">
				<ul class="nav flex-column">
					<li class="nav-item">
						<InertiaLink :href="route('universes.series.index', [universe])" class="nav-link">
							Series
						</InertiaLink>
					</li>
					<li class="nav-item">
						<InertiaLink :href="route('universes.teams.index', [universe])" class="nav-link">
							Teams
						</InertiaLink>
					</li>
					<li class="nav-item">
						<InertiaLink :href="route('universes.drivers.index', [universe])" class="nav-link" href="#">
							Drivers
						</InertiaLink>
					</li>
				</ul>
			</div>
		</div>
	</Base>
</template>

<script setup>
import Base from './Base';

const props = defineProps({
	universe: {
		type: Object,
		required: true,
	},
});
</script>

<script>
export default { name: 'UniverseLayout' };
</script>
