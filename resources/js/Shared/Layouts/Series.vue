<template>
	<Base>
		<div class="wrapper d-flex">
			<div class="w-100 mt-5 bg-dark p-4">
				<h1>{{ series.name }}</h1>
				<slot/>
			</div>
			<div class="w-25 ms-5 mt-5 bg-dark p-4">
				<ul class="nav flex-column">
					<li class="nav-item">
						<InertiaLink :href="route('series.engines.index', [series])" class="nav-link">
							Engines
						</InertiaLink>
					</li>
					<li class="nav-item">
						<InertiaLink :href="route('series.seasons.index', [series])" class="nav-link">
							Seasons
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
	series: {
		type: Object,
		required: true,
	},
});
</script>

<script>
export default { name: 'SeriesLayout' };
</script>
