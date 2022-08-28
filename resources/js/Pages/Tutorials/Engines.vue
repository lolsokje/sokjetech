<template>
    <h3>Engines</h3>

    <BackLink :backTo="route('tutorials', 'series')" label="series tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Engines added to series can later be
        <InertiaLink :href="route('tutorials', 'season/engines')">added</InertiaLink>
        to specific seasons. They're specifically added to series so you don't have to keep adding the same engines to
        each new season, however it's very unlikely for the same engines to be used in different series.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import TutorialPageList from '@/Components/TutorialPageList';

const pages = [
    new TutorialPageLink('season/engines', 'Adding engines to a season'),
];
</script>
