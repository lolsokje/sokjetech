<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="w-100 bg-dark p-4">
            <h1>{{ race.name }}</h1>
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Layouts/Base';
import TabLinks from '@/Components/TabLinks';
import { TabLink } from '@/Utilities/TabLink';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
});

const links = [
    new TabLink('weekend.intro', 'Intro', [ props.race ]),
    new TabLink('weekend.qualifying', 'Qualifying', [ props.race ]),
    new TabLink('weekend.grid', 'Grid', [ props.race ]),
    new TabLink('weekend.race', 'Race', [ props.race ]),
    new TabLink('weekend.results', 'Results', [ props.race ]),
];
</script>

<script>
export default { name: 'RaceWeekendLayout' };
</script>
