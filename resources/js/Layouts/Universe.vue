<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="st-card p-4">
            <h1>{{ universe.name }}</h1>
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from './Base.vue';
import { TabLink } from '@/Utilities/TabLink';
import TabLinks from '@/Components/TabLinks.vue';

const props = defineProps({
    universe: {
        type: Object,
        required: true,
    },
});

const links = [
    new TabLink('universes.series.index', 'Series', [ props.universe ]),
    new TabLink('universes.teams.index', 'Teams', [ props.universe ]),
    new TabLink('universes.drivers.index', 'Drivers', [ props.universe ]),
];
</script>

<script>
export default { name: 'UniverseLayout' };
</script>
