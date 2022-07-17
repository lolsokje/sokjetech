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
                    <li class="nav-item">
                        <a href="#" class="nav-link">Race</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Debrief</a>
                    </li>
                </ul>
            </div>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Shared/Layouts/Base';
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
