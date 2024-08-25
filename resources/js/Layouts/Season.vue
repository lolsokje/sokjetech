<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="st-card p-4">
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from './Base.vue';
import TabLinks from '@/Components/TabLinks.vue';
import { TabLink } from '@/Utilities/TabLink';
import { onMounted } from 'vue';
import { seasonStore } from '@/Stores/seasonStore';

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

const canEdit = props.can.edit;
const showLink = canEdit;

const standingsLink = new TabLink(null, 'Standings');

standingsLink.addChildren(
    new TabLink('seasons.standings.drivers', 'Drivers', [ props.season ]),
    new TabLink('seasons.standings.teams', 'Teams', [ props.season ]),
);

const seasonSetupLink = new TabLink(null, 'Entries');

seasonSetupLink.addChildren(
    new TabLink('seasons.entrants.index', 'Teams', [ props.season ]),
    new TabLink('seasons.racers.index', 'Drivers', [ props.season ]),
    new TabLink('seasons.engines.index', 'Engines', [ props.season ]),
);

const developmentLink = new TabLink('seasons.development.show', 'Development', [ props.season ], showLink);

const configurationLink = new TabLink(null, 'Configuration', [], showLink);

if (configurationLink.show) {
    configurationLink.addChildren(
        new TabLink('seasons.configuration.points', 'Points', [ props.season ]),
        new TabLink('seasons.configuration.qualifying', 'Qualifying', [ props.season ]),
        new TabLink('seasons.configuration.reliability', 'Reliability', [ props.season ]),
    );
}

const links = [
    standingsLink,
    new TabLink('seasons.races.index', 'Calendar', [ props.season ]),
    seasonSetupLink,
    developmentLink,
    new TabLink('seasons.history.show', 'Dev history', [ props.season ]),
    configurationLink,
];

onMounted(() => {
    if (! seasonStore.season) {
        seasonStore.season = props.season;
    }
});
</script>

<script>
export default { name: 'SeasonLayout' };
</script>
