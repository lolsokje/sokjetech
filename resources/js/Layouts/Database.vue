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
import Base from '@/Layouts/Base';
import { TabLink } from '@/Utilities/TabLink';
import TabLinks from '@/Components/TabLinks';

const links = [
    new TabLink('database.circuits.index', 'Circuits'),
];
</script>

<script>
export default { name: 'Database' };
</script>
