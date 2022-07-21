<template>
    <BackLink :backTo="route('series.seasons.index', [season.series])" label="series overview"/>

    <h3>Races</h3>

    <InertiaLink v-if="canAddRace()" :href="route('seasons.races.create', [season])" class="btn btn-primary my-3">
        Add race
    </InertiaLink>
    <InertiaLink
        v-if="canReorderRaces()" :href="route('seasons.races.reorder', [season])"
        class="btn btn-primary my-3 ms-3"
    >
        Reorder races
    </InertiaLink>

    <table class="table table-bordered table-dark" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th>Name</th>
            <th class="text-center">Completed</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="race in season.races" :key="race.id">
            <td class="small-centered">{{ race.order }}</td>
            <td class="padded-left">{{ race.name }}</td>
            <td class="big-centered">
                <fa v-if="race.completed" icon="check"/>
                <fa v-else icon="times"/>
            </td>
            <td class="small-centered">
                <InertiaLink v-if="canEditRace(race)" :href="route('seasons.races.edit', [season, race])">
                    edit
                </InertiaLink>
                <InertiaLink v-else-if="isNextRace(race)" :href="route('weekend.intro', [race])">start</InertiaLink>
                <InertiaLink v-if="race.completed" :href="route('weekend.results', [race])">results</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    next_race_id: {
        type: String,
        required: false,
    },
    can: {
        type: Object,
        required: true,
    },
});

const canAddRace = () => {
    return props.can.edit && !props.season.started;
};

const canReorderRaces = () => {
    return canAddRace() && props.season.races.length > 1;
};

const canEditRace = (race) => {
    return canAddRace() && !race.completed;
};

const isNextRace = (race) => {
    return race.id === props.next_race_id;
};
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
