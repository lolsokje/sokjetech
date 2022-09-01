<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="st-card p-4">
            <h1>{{ season.full_name }}</h1>
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from './Base';
import TabLinks from '@/Components/TabLinks';
import { TabLink } from '@/Utilities/TabLink';

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

const developmentLink = new TabLink(null, 'Development', [], showLink);

if (developmentLink.show) {
    developmentLink.addChildren(
        new TabLink('seasons.development.drivers', 'Drivers', [ props.season ]),
        new TabLink('seasons.development.teams', 'Teams', [ props.season ]),
        new TabLink('seasons.development.engines', 'Engines', [ props.season ]),
    );
}

const reliabilityLink = new TabLink(null, 'Reliability', [], showLink);

if (reliabilityLink.show) {
    reliabilityLink.addChildren(
        new TabLink('seasons.development.reliability.drivers', 'Drivers', [ props.season ]),
        new TabLink('seasons.development.reliability.teams', 'Teams', [ props.season ]),
        new TabLink('seasons.development.reliability.engines', 'Engines', [ props.season ]),
    );
}

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
    reliabilityLink,
    configurationLink,
];
</script>

<script>
export default { name: 'SeasonLayout' };
</script>
