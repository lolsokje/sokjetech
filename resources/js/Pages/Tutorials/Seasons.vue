<template>
    <h3>Seasons</h3>

    <BackLink :backTo="route('tutorials', 'series')" label="series tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Seasons are perhaps the most important part of SokjeTech, as they are where you actually build your sim. Within
        a season you'll define a
        <InertiaLink :href="route('tutorials', 'season/calendar')">calender</InertiaLink>
        (including stints and their RNG for each race),
        <InertiaLink :href="route('tutorials', 'season/entrants')">teams</InertiaLink>
        and their
        <InertiaLink :href="route('tutorials', 'season/racers')">drivers</InertiaLink>
        and finally
        <InertiaLink :href="route('tutorials', 'season/configurations')">configurations</InertiaLink>
        for things like the point system, qualifying format and reliability.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"></TutorialPageList>
</template>

<script setup>
import TutorialPageList from '@/Components/TutorialPageList.vue';
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import BackLink from '@/Shared/BackLink.vue';

const pages = [
    new TutorialPageLink('season/entrants', 'Entrants'),
    new TutorialPageLink('season/racers', 'Racers'),
    new TutorialPageLink('season/calendar', 'Calendar'),
    new TutorialPageLink('season/configurations', 'Configurations'),
    new TutorialPageLink('season/development', 'Development'),
];
</script>
