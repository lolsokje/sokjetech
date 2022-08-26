<template>
    <h3>Teams</h3>

    <BackLink :backTo="route('tutorials', 'universes')" label="universe tutorial page"/>

    <p>
        Teams are core participants in your sims. A team can exist in multiple
        <InertiaLink :href="route('tutorials', 'series')">series</InertiaLink>
        at the same time, however their stats will change between each series and
        <InertiaLink :href="route('tutorials', 'seasons')">season</InertiaLink>
        they participate in. Teams are universe-wide entities to allow them to participate in multiple series at the
        same time, such as Prema and ART throughout the FIA Feeder Series ladder, while still having a central entity to
        track stats and results for.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import TutorialPageList from '@/Components/TutorialPageList';

const pages = [
    { link: route('tutorials', 'drivers'), label: 'Drivers' },
    { link: route('tutorials', 'entrants'), label: 'Entrants' },
];
</script>
