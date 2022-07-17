<template>
    <BackLink :backTo="route('series.seasons.show', [season.series, season])" label="season overview"/>

    <h3>Engines</h3>

    <InertiaLink v-if="can.edit" :href="route('seasons.engines.create', [season])" class="btn btn-primary my-3">
        Add engine to season
    </InertiaLink>

    <table class="table table-bordered table-dark table-narrow" id="screenshot-target">
        <thead>
        <tr>
            <th>Engine</th>
            <th class="text-center">Re-badged</th>
            <th>Re-badged from</th>
            <th class="text-center">Rating</th>
            <th class="text-center">Reliability</th>
            <th v-if="can.edit"></th>
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
            <td v-if="can.edit" class="small-centered">
                <InertiaLink :href="route('seasons.engines.edit', [season, engine])">edit</InertiaLink>
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
    engines: {
        type: Array,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});
</script>

<script>
import Season from '@/Shared/Layouts/Season';

export default {layout: Season};
</script>

