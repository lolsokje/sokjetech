<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h3>Entrants</h3>

    <InertiaLink v-if="can.edit" :href="route('seasons.entrants.create', [season])" class="btn btn-primary mb-3">
        Add entrant
    </InertiaLink>

    <ActiveRaceWarning v-if="hasActiveRace"/>

    <table class="table table-bordered table-dark" id="screenshot-target">
        <thead>
        <tr>
            <th>Full name</th>
            <th>Short name</th>
            <th>Team principal</th>
            <th>Engine supplier</th>
            <th class="text-center">Country</th>
            <th colspan="2" v-if="canEdit"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="entrant in season.entrants" :key="entrant.id">
            <td :style="entrant.style_string" class="padded-left">
                {{ entrant.full_name }}
            </td>
            <td class="padded-left">{{ entrant.short_name }}</td>
            <td class="padded-left">{{ entrant.team_principal }}</td>
            <td class="padded-left">
				<span v-if="entrant.engine">
					{{ entrant.engine.name }}
				</span>
            </td>
            <td class="small-centered">{{ entrant.country }}</td>
            <template v-if="canEdit">
                <td class="small-centered">
                    <InertiaLink :href="route('seasons.entrants.edit', [season, entrant])">edit</InertiaLink>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('seasons.racers.create', [season, entrant])">drivers</InertiaLink>
                </td>
            </template>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning';

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

const hasActiveRace = props.season.has_active_race;
const canEdit = props.can.edit && !hasActiveRace;
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default { layout: Season };
</script>
