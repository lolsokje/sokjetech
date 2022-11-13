<template>
    <h3>Universes</h3>

    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Universes are the core of SokjeTech sims, everything a sim needs will exist within one. While universes
        themselves are quite easy to understand (you can see a universe as being governed by the FIA), there's quite a
        lot you need to know about their underlying functionality and requirements;
    </p>

    <p>
        Each universe has one of three "visibilities". "Public" universes can be seen by anyone, regardless of being
        logged in, while "private" universes can only be seen by the universe's creator (or "owner"). Lastly, "logged in
        only" universes requires someone to be logged in to see the universe. Keep this in mind when you're creating
        your universe. If you don't want participants F5-ing the page to see the race result before you post it, don't
        make your universe public!
    </p>

    <TutorialPageList :pages="pages" title="Further reading:"/>
</template>

<script setup>
import TutorialPageList from '@/Components/TutorialPageList.vue';
import TutorialPageLink from '@/Utilities/TutorialPageLink';
import BackLink from '@/Shared/BackLink.vue';

const pages = [
    new TutorialPageLink('teams', 'Teams'),
    new TutorialPageLink('drivers', 'Drivers'),
    new TutorialPageLink('series', 'Series'),
];
</script>
