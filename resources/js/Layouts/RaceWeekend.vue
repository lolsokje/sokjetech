<template>
    <Base>
        <div class="wrapper d-flex">
            <div class="w-100 mt-5 bg-dark p-4">
                <h1>{{ race.name }}</h1>
                <slot/>
            </div>
            <div class="w-25 ms-5 mt-5 bg-dark p-4">
                <p class="mb-0 ps-3">Pages</p>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <InertiaLink :href="route('weekend.intro', [race])" class="nav-link">
                            Intro
                        </InertiaLink>
                    </li>
                    <li class="nav-item">
                        <InertiaLink :href="route('weekend.qualifying', [race])" class="nav-link">
                            Qualifying
                        </InertiaLink>
                    </li>
                    <li class="nav-item" v-if="race.qualifying_completed">
                        <InertiaLink :href="route('weekend.grid', [race])" class="nav-link">
                            Starting grid
                        </InertiaLink>
                    </li>
                    <li class="nav-item" v-if="race.qualifying_completed">
                        <InertiaLink :href="route('weekend.race', [race])" class="nav-link">
                            Race
                        </InertiaLink>
                    </li>
                    <li class="nav-item" v-if="race.completed">
                        <InertiaLink :href="route('weekend.results', [race])" class="nav-link">
                            Results
                        </InertiaLink>
                    </li>
                </ul>
            </div>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Layouts/Base';
import { defineProps } from 'vue';

defineProps({
    race: {
        type: Object,
        required: true,
    },
});
</script>

<script>
export default { name: 'RaceWeekendLayout' };
</script>
