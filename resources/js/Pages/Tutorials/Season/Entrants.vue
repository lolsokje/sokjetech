<template>
    <h3>Entrants</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>

    <p>
        To differentiate between global "teams" in a universe, and "teams" in a season, I've decided to name teams
        taking part in the season "entrants".
    </p>

    <p>
        To add an entrant to the current season, you'll have to select a base team first. Doing so will automatically
        pre-fill all required fields with the details of the selected team, however you're free to edit any of these
        details as you feel. They won't affect the base team's details, only this entrant. If you've added
        <InertiaLink :href="route('tutorials', 'season/engines')">engines</InertiaLink>
        to the season, you can select one here as well.
    </p>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';</script>
