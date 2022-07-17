<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h3>Drivers</h3>

    <template v-if="can.edit">
        <input id="edit-mode" v-model="editMode" class="mb-3 form-check-inline" type="checkbox">
        <label class="form-check-label" for="edit-mode">Edit mode?</label>
    </template>

    <table class="table table-bordered table-dark">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="colour-accent"></th>
            <th>Driver</th>
            <th v-if="canEdit">Manage</th>
            <th class="text-center">#</th>
            <th>Team name</th>
            <th v-if="canEdit">Manage</th>
            <th class="text-center">Team</th>
            <th class="text-center">Driver</th>
            <th class="text-center">Engine</th>
            <th class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(driver, key) in drivers" :key="driver.id">
            <td class="small-centered">{{ key + 1 }}</td>
            <td class="colour-accent" :style="`background-color: ${driver.entrant.primary_colour}`"></td>
            <td class="padded-left">{{ driver.driver_name }}</td>
            <td v-if="canEdit" class="small-centered">
                <InertiaLink :href="route('seasons.racers.create', [season, driver.entrant])">
                    driver
                </InertiaLink>
            </td>
            <td :style="driver.style_string" class="small-centered">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td v-if="canEdit" class="small-centered">
                <InertiaLink :href="route('seasons.entrants.edit', [season, driver.entrant])">
                    entrant
                </InertiaLink>
            </td>
            <td class="small-centered">{{ driver.team_rating }}</td>
            <td class="small-centered">{{ driver.driver_rating }}</td>
            <td class="small-centered">{{ driver.engine_rating }}</td>
            <td class="small-centered">{{ driver.total_rating }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import BackLink from '@/Shared/BackLink';

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    racers: {
        type: Array,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});

const drivers = ref([]);
const editMode = ref(false);
const canEdit = ref(props.can.edit && editMode);

onMounted(() => {
    props.racers.forEach((racer) => {
        const entrant = racer.entrant;
        const engineRating = entrant.engine.rating;
        const totalRating = racer.rating + entrant.rating + engineRating;

        drivers.value.push({
            style_string: entrant.style_string,
            team_name: entrant.full_name,
            team_rating: entrant.rating,
            driver_name: racer.driver.full_name,
            number: racer.number,
            driver_rating: racer.rating,
            engine_rating: engineRating,
            total_rating: totalRating,
            entrant: entrant,
        });
    });

    drivers.value.sort((a, b) => {
        return a.total_rating < b.total_rating;
    });

    editMode.value = localStorage.getItem('racers_edit_mode') === 'true';
});

watch(editMode, () => {
    localStorage.setItem('racers_edit_mode', editMode.value.toString());
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default {layout: Season};
</script>
