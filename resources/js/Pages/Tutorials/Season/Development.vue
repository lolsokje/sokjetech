<template>
    <h3>Development</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        There's two different types of development in SokjeTech; rating development and reliability development. Each of
        these two types of development can be performed for drivers, teams and engines (these will be referred to as
        "entities" in the remainder of this tutorial). While rating and reliability development affect different
        ratings, the underlying functionality is identical;
    </p>

    <p>
        The quickest way of performing development is by setting the minimum and maximum RNG values and then clicking
        "Apply" to apply the selected ranges to all entities. Once you've done that, click "Run dev" to
        perform the RNG roll, and finally "Save dev" to update the ratings. Before being able to save ratings, you'll
        first have to perform a development roll.
    </p>

    <p>
        Aside from a general range for each entity, you can also customise the range for each individual entity by using
        the input fields under the "Min" and "Max" table columns.
    </p>

    <p>
        Finally, you can edit an entities rating directly by ticking the "Edit ratings directly" checkbox. Doing so will
        also allow you to save ratings directly without first having to perform a development roll, like you'd have to
        with "normal" development.
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import TutorialPageList from '@/Components/TutorialPageList';

const pages = [
    new TutorialPageLink('season/entrants', 'Entrants'),
    new TutorialPageLink('season/racers', 'Racers'),
    new TutorialPageLink('season/engines', 'Adding engines to a season'),
];
</script>
