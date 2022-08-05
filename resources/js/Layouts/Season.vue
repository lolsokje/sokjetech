<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="w-100 bg-dark p-4">
            <h1>{{ season.full_name }} season</h1>
            <button v-if="!season.started && can.edit" class="btn btn-success"
                    @click.prevent="confirmSeasonStart()">
                Start season
            </button>
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from './Base';
import { Inertia } from '@inertiajs/inertia';
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

const confirmSeasonStart = () => {
    if (!confirm('Are you sure you want to start the season? You will no longer be able to modify the calendar, qualifying format and point system')) {
        return;
    }

    Inertia.put(route('seasons.start', [ props.season ]));
};

const showLink = props.can.edit;

const standingsLink = new TabLink(null, 'Standings');

standingsLink.addChildren(
    new TabLink('seasons.standings.drivers', 'Drivers', [ props.season ]),
    new TabLink('seasons.standings.teams', 'Teams', [ props.season ]),
);

const seasonSetupLink = new TabLink(null, 'Season setup');

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
