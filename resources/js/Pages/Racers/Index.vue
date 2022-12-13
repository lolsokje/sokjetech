<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h3>Drivers</h3>

    <template v-if="can.edit && !hasActiveRace">
        <input id="edit-mode" v-model="editMode" class="mb-3 form-check-inline" type="checkbox">
        <label class="form-check-label" for="edit-mode">Edit mode?</label>
    </template>

    <ActiveRaceWarning v-if="hasActiveRace"/>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="colour-accent"></th>
            <th>Driver</th>
            <th v-if="canEdit">Manage</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
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
            <td class="smallest-centered">{{ key + 1 }}</td>
            <BackgroundColourCell :backgroundColour="driver.entrant.accent_colour"/>
            <td class="padded-left">{{ driver.driver_name }}</td>
            <td v-if="canEdit" class="small-centered">
                <InertiaLink :href="route('seasons.racers.create', [season, driver.entrant])">
                    driver
                </InertiaLink>
            </td>
            <td class="small-centered">
                <CountryFlag :country="driver.country"/>
            </td>
            <td :style="driver.style_string" class="smallest-centered">{{ driver.number }}</td>
            <td class="padded-left">{{ driver.team_name }}</td>
            <td v-if="canEdit && !hasActiveRace" class="small-centered">
                <InertiaLink :href="route('seasons.entrants.edit', [season, driver.entrant])">
                    entrant
                </InertiaLink>
            </td>
            <td class="small-centered bg-accent-odd">{{ driver.team_rating }}</td>
            <td class="small-centered">{{ driver.driver_rating }}</td>
            <td class="small-centered bg-accent-odd">{{ driver.engine_rating }}</td>
            <td class="small-centered bg-accent-even">{{ driver.total_rating }}</td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import { onMounted, Ref, ref, watch } from 'vue';
import BackLink from '@/Shared/BackLink.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import SeasonInterface from '@/Interfaces/Season';
import Racer from '@/Interfaces/Racer';
import Permission from '@/Interfaces/Permission';
import Entrant from '@/Interfaces/Entrant';

interface Props {
    season: SeasonInterface,
    racers: Racer[],
    can: Permission,
}

interface Driver {
    style_string: string,
    team_name: string,
    team_rating: number,
    driver_name: string,
    country: string,
    number: number,
    driver_rating: number,
    engine_rating: number,
    total_rating: number,
    entrant: Entrant,
}

const props = defineProps<Props>();

const hasActiveRace = props.season.has_active_race;
const drivers: Ref<Driver[]> = ref([]);
const editMode = ref(false);
const canEdit: Ref<boolean> = ref(props.can.edit && editMode);

onMounted(() => {
    props.racers.forEach((racer: Racer) => {
        const entrant = racer.entrant;
        const engineRating = entrant.engine?.rating ?? 0;
        const totalRating = racer.rating + entrant.rating + engineRating;

        // TODO: move to Laravel API Resource
        drivers.value.push({
            style_string: entrant.style_string,
            team_name: entrant.full_name,
            team_rating: entrant.rating,
            driver_name: racer.driver.full_name,
            country: racer.driver.country,
            number: racer.number,
            driver_rating: racer.rating,
            engine_rating: engineRating,
            total_rating: totalRating,
            entrant: entrant,
        });
    });

    drivers.value.sort((a, b) => {
        return b.total_rating - a.total_rating;
    });

    editMode.value = localStorage.getItem('racers_edit_mode') === 'true';
});

watch(editMode, () => {
    localStorage.setItem('racers_edit_mode', editMode.value.toString());
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
