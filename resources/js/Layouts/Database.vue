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
import Base from '@/Layouts/Base.vue';
import { TabLink } from '@/Utilities/TabLink';
import TabLinks from '@/Components/TabLinks.vue';

const links = [
    new TabLink('database.circuits.index', 'Circuits'),
    new TabLink('database.teams.index', 'Teams'),
    new TabLink('database.drivers.index', 'Drivers'),
    new TabLink('database.engines.index', 'Engines'),
];
</script>

<script>
export default { name: 'Database' };
</script>
