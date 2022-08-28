<template>
    <h3>Simming races</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Click the buttons in the correct order, it's that simple.
    </p>
</template>

<script setup>

import BackLink from '@/Shared/BackLink';</script>
