<template>
	<Base>
		<div class="wrapper d-flex">
			<div class="w-100 mt-5 bg-dark p-4">
				<h1>{{ season.full_name }}</h1>
				<slot/>
			</div>
			<div class="w-25 ms-5 mt-5 bg-dark p-4">
				<ul class="nav flex-column">
					<li class="nav-item">
						<InertiaLink :href="route('seasons.races.index', [season])" class="nav-link">Races</InertiaLink>
					</li>
					<li class="nav-item">
						<InertiaLink :href="route('seasons.entrants.index', [season])" class="nav-link">Entrants
						</InertiaLink>
					</li>
				</ul>
			</div>
		</div>
	</Base>
</template>

<script setup>
import Base from './Base';

defineProps({
	season: {
		type: Object,
		required: true,
	},
});
</script>

<script>
export default { name: 'SeasonLayout' };
</script>
