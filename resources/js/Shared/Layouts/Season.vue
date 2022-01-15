<template>
	<Base>
		<div class="wrapper d-flex">
			<div class="w-100 mt-5 bg-dark p-4">
				<h1>{{ season.full_name }} season</h1>
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
					<li class="nav-item">
						<InertiaLink :href="route('seasons.racers.index', [season])" class="nav-link">
							Drivers
						</InertiaLink>
					</li>
				</ul>

				<p class="mt-2 mb-0 ps-3">Development</p>
				<ul class="nav flex-column">
					<li class="nav-item">
						<InertiaLink :href="route('seasons.development.drivers', [season])" class="nav-link">
							Driver
						</InertiaLink>
					</li>
					<li class="nav-item">
						<InertiaLink :href="route('seasons.development.teams', [season])" class="nav-link">
							Teams
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
