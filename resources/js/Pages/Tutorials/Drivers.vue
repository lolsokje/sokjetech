<template>
    <h3>Drivers</h3>

    <BackLink :backTo="route('tutorials', 'universes')" label="universe tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Drivers are core participants in your sims. A driver can exist in multiple
        <InertiaLink :href="route('tutorials', 'series')">series</InertiaLink>
        at the same time, however their stats will change between each series and
        <InertiaLink :href="route('tutorials', 'seasons')">season</InertiaLink>
        they participate in. Drivers universe-wide entities to allow them to progress naturally through their career,
        like a real-life driver would from F4, to F3, to F2 and eventually F1, while still having a central entity to
        track stats and results for.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import TutorialPageList from '@/Components/TutorialPageList.vue';
import TutorialPageLink from '@/Utilities/TutorialPageLink';

const pages = [
    new TutorialPageLink('teams', 'Teams'),
    new TutorialPageLink('season/racers', 'Racers'),
];
</script>
