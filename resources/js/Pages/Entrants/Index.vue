<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                :labelLink="route('seasons.races.index', season)"
                append="Entrants"
    />

    <InertiaLink v-if="can.edit" :href="route('seasons.entrants.create', [season])" class="btn btn-primary mb-3">
        Add entrant
    </InertiaLink>

    <ActiveRaceWarning v-if="hasActiveRace"/>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="colour-accent"></th>
            <th>Full name</th>
            <th>Short name</th>
            <th>Team principal</th>
            <th>Engine supplier</th>
            <th class="text-center"></th>
            <th colspan="2" v-if="canEdit"></th>
            <th v-if="canDeleteTeam"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="entrant in season.entrants" :key="entrant.id">
            <BackgroundColourCell :backgroundColour="entrant.accent_colour"/>
            <td class="padded-left">{{ entrant.full_name }}</td>
            <td class="padded-left">{{ entrant.short_name }}</td>
            <td class="padded-left">{{ entrant.team_principal }}</td>
            <td class="padded-left">
				<span v-if="entrant.engine">
					{{ entrant.engine.name }}
				</span>
            </td>
            <td class="small-centered">
                <CountryFlag :country="entrant.country"/>
            </td>
            <template v-if="canEdit">
                <td class="small-centered">
                    <InertiaLink :href="route('seasons.entrants.edit', [season, entrant])">edit</InertiaLink>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('seasons.racers.create', [season, entrant])">drivers</InertiaLink>
                </td>
            </template>
            <td v-if="canDeleteTeam" class="small-centered">
                <button class="btn btn-link" @click.prevent="deleteEntrant(entrant)">
                    delete
                </button>
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import SeasonInterface from '@/Interfaces/Season';
import Permission from '@/Interfaces/Permission';
import Entrant from '@/Interfaces/Entrant';
import { router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    season: SeasonInterface,
    can: Permission,
}

const props = defineProps<Props>();

const hasActiveRace = props.season.has_active_race;
const canEdit = props.can.edit && ! hasActiveRace;
const canDeleteTeam = props.can.edit && ! props.season.started;

const deleteEntrant = (entrant: Entrant): void => {
    if (! confirm(`Are you sure you want to remove "${entrant.full_name}"? This will also delete any associated drivers`)) {
        return;
    }

    router.delete(route('seasons.entrants.destroy', [ props.season, entrant ]));
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
