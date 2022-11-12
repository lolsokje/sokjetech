<template>
    <h3>Adding engines to a season</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Engines aren't required to run a season, but they can add some more variety. In order to assign them to an
        entrant, you first have to add engines to the current season. This can be done under "Entries" > "Engines" by
        clicking the "Add engine to season" button.
    </p>

    <p>
        You first have to select a base engine, which you'll have created in a specific series. After selecting a base
        engine, you get the option to "rebadge" engines. Rebadged engines use the same base engine, but allow you to
        rename (thus rebadge) the engine as well as perform engine rating- and reliability-development separately from
        the main engine.
    </p>

    <p>
        After adding engines to the season, you can add them to teams by simply selecting an engine supplier when
        creating or editing an entrant.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink.vue';
import TutorialPageList from '@/Components/TutorialPageList.vue';
import TutorialPageLink from '@/Utilities/TutorialPageLink';

const pages = [
    new TutorialPageLink('season/entrants', 'Entrants'),
    new TutorialPageLink('engines', 'Engines (series)'),
];
</script>
