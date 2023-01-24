<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h3>Engines</h3>

    <InertiaLink v-if="canEdit" :href="route('seasons.engines.create', [season])" class="btn btn-primary my-3">
        Add engine to season
    </InertiaLink>

    <ActiveRaceWarning v-if="hasActiveRace"/>

    <table class="table table-narrow" id="screenshot-target">
        <thead>
        <tr>
            <th>Engine</th>
            <th class="text-center">Re-badged</th>
            <th>Re-badged from</th>
            <th class="text-center">Rating</th>
            <th class="text-center">Reliability</th>
            <th v-if="canEdit"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="engine in engines" :key="engine.id">
            <td class="padded-left">{{ engine.name }}</td>
            <td class="text-center">{{ engine.rebadge ? 'Yes' : 'No' }}</td>
            <td class="padded-left">
				<span v-if="engine.rebadge">
					{{ engine.base_engine.name }}
				</span>
                <span v-else>
					-
				</span>
            </td>
            <td class="text-center">{{ engine.rating }}</td>
            <td class="text-center">{{ engine.reliability }}</td>
            <td v-if="canEdit" class="small-centered">
                <InertiaLink :href="route('seasons.engines.edit', [season, engine])">edit</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import BackLink from '@/Shared/BackLink.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import Permission from '@/Interfaces/Permission';
import SeasonEngine from '@/Interfaces/SeasonEngine';

interface Props {
    season: Season,
    engines: SeasonEngine[],
    can: Permission,
}

const props = defineProps<Props>();

const hasActiveRace = props.season.has_active_race;
const canEdit = props.can.edit && !hasActiveRace;
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
