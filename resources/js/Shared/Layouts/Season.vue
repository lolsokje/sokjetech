<template>
    <Base>
        <div class="wrapper d-flex">
            <div class="w-100 mt-5 bg-dark p-4">
                <h1>{{ season.full_name }} season</h1>
                <button v-if="!season.started && can.edit" class="btn btn-success" @click.prevent="confirmSeasonStart()">
                    Start season
                </button>
                <slot/>
            </div>
            <div class="w-25 ms-5 mt-5 bg-dark p-4">
                <p class="mb-0 ps-3">Basic setup</p>
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
                    <li class="nav-item">
                        <InertiaLink :href="route('seasons.engines.index', [season])" class="nav-link">
                            Engines
                        </InertiaLink>
                    </li>
                </ul>

                <template v-if="can.edit">
                    <p class="mt-3 mb-0 ps-3">Development</p>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <InertiaLink :href="route('seasons.development.drivers', [season])" class="nav-link">
                                Drivers
                            </InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink :href="route('seasons.development.teams', [season])" class="nav-link">
                                Teams
                            </InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink :href="route('seasons.development.engines', [season])" class="nav-link">
                                Engines
                            </InertiaLink>
                        </li>
                    </ul>

                    <p class="mt-3 mb-0 ps-3">Reliability</p>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <InertiaLink
                                :href="route('seasons.development.reliability.drivers', [season])"
                                class="nav-link"
                            >
                                Drivers
                            </InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink
                                :href="route('seasons.development.reliability.teams', [season])"
                                class="nav-link"
                            >
                                Teams
                            </InertiaLink>
                        </li>
                    </ul>

                    <p class="mt-3 mb-0 ps-3">Configuration</p>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <InertiaLink :href="route('seasons.configuration.points', [season])" class="nav-link">
                                Points
                            </InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink :href="route('seasons.configuration.qualifying', [season])" class="nav-link">
                                Qualifying
                            </InertiaLink>
                        </li>
                    </ul>
                </template>
            </div>
        </div>
    </Base>
</template>

<script setup>
import Base from './Base';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});

const confirmSeasonStart = () => {
    if (!confirm('Are you sure you want to start the season? You will no longer be able to modify the calendar, qualifying format and point system')) {
        return;
    }

    Inertia.put(route('seasons.start', [ props.season ]));
};
</script>

<script>
export default { name: 'SeasonLayout' };
</script>
