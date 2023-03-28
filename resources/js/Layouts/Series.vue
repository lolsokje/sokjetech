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

const props = defineProps({
    series: {
        type: Object,
        required: true,
    },
});

const links = [
    new TabLink('series.seasons.index', 'Seasons', [ props.series ]),
    new TabLink('series.engines.index', 'Engines', [ props.series ]),
];
</script>

<script>
export default { name: 'SeriesLayout' };
</script>
