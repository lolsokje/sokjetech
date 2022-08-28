<template>
    <h3>Circuits</h3>

    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Circuits are required to be added before being able to add races to a season's calendar. Once a circuit has been
        created, you can reuse them in all of your universes.
    </p>
</template>

<script setup>

import BackLink from '@/Shared/BackLink';</script>
