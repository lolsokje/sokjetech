<template>
    <h3>Calendar</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Each race on the calendar takes place at a
        <InertiaLink :href="route('tutorials', 'circuits')">circuit</InertiaLink>
        you will have created at an earlier stage. Aside from selecting a circuit and giving the race a name, you'll
        also have to define at least one stint. A stint consists of a few settings:
    </p>

    <ul>
        <li><strong>Min and max RNG</strong>: the actual RNG ranges used for this stint's RNG roll</li>
        <li><strong>DNF chance</strong>: whether or not drivers can DNF during this stint</li>
        <li>
            <strong>Use team rating</strong>: whether or not to include the team's rating in the total roll for this
            stint
        </li>
        <li>
            <strong>Use driver rating</strong>: whether or not to include the driver's rating in the total roll for this
            stint
        </li>
        <li>
            <strong>Use engine rating</strong>: whether or not to include the engine's rating in the total roll for this
            stint
        </li>
    </ul>

    <p>
        Ticking the "use [xyz] rating" boxes can be a great way of introducing some bias in RNG rolls and making a
        team's/driver's/engine's rating matter more during the race.
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
