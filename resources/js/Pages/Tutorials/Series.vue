<template>
    <h3>Series</h3>

    <p>
        Each series exists within a given universe, in Formula One's case for example, "Formula One" would be the series
        name while "FIA" would be the universe. A series itself is quite simple to create as all it needs is a name.
    </p>

    <p>
        Series are also where engines are stored, however they are not required to actually run a sim. As with
        <InertiaLink :href="route('tutorials', 'teams')">teams</InertiaLink>
        and
        <InertiaLink :href="route('tutorials', 'drivers')">drivers</InertiaLink>
        within a universe, engines defined in a series can be used throughout multiple different seasons. How to
        actually use an engine in a season is explained on
        <InertiaLink :href="route('tutorials', 'engines/seasons')">this</InertiaLink>
        page.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import TutorialPageList from '@/Components/TutorialPageList';

const pages = [
    { link: route('tutorials', 'engines'), label: 'Engines' },
    { link: route('tutorials', 'seasons'), label: 'Seasons' },
];
</script>
