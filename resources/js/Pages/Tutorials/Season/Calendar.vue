<template>
    <h3>Calendar</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Each race on the calendar takes place at a
        <InertiaLink :href="route('tutorials', 'circuits')">circuit</InertiaLink>
        you will have created at an earlier stage. After selecting a circuit, you'll have to select the variation you
        want to use for the race you're creating.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import TutorialPageList from '@/Components/TutorialPageList.vue';

const pages = [
    new TutorialPageLink('circuits', 'Circuits'),
    new TutorialPageLink('season/races', 'Simming races'),
];
</script>
