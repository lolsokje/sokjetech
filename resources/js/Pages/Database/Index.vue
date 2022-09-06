<template>
    <h3>Global shared database</h3>

    <p class="col-6">
        The global shared database allows you to copy circuits, teams, drivers and engines created and shared by others
        to your own universes, to speed up the process of setting up a new sim. At this point it's only possible to copy
        teams, drivers and engines to universes/series as the process of adding those to an actual season is already
        quite simple.
    </p>
</template>

<script>
import Database from '@/Layouts/Database';
import Base from '@/Layouts/Base';

export default {
    components: { Base },
    layout: Database,
};
</script>
