<template>
    <h3>Tutorials</h3>

    <TutorialPageList :pages="pages" classes="list-unstyled"/>
</template>

<script setup>
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import TutorialPageList from '@/Components/TutorialPageList.vue';

const pages = [
    new TutorialPageLink('circuits', 'Circuits'),
    new TutorialPageLink('universes', 'Universes', [
        new TutorialPageLink('series', 'Series', [
            new TutorialPageLink('seasons', 'Seasons', [
                new TutorialPageLink('season/calendar', 'Calendar'),
                new TutorialPageLink('season/entrants', 'Entrants'),
                new TutorialPageLink('season/racers', 'Racers'),
                new TutorialPageLink('season/engines', 'Adding engines to seasons'),
                new TutorialPageLink('season/development', 'Development'),
                new TutorialPageLink('season/configurations', 'Configurations'),
                new TutorialPageLink('season/races', 'Simming races'),
            ]),
            new TutorialPageLink('engines', 'Engines'),
        ]),
        new TutorialPageLink('teams', 'Teams', [
            new TutorialPageLink('season/entrants', 'Entrants'),
        ]),
        new TutorialPageLink('drivers', 'Drivers', [
            new TutorialPageLink('season/racers', 'Racers'),
        ]),
    ]),
];
</script>
