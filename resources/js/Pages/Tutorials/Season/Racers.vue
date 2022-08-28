<template>
    <h3>Racers</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorial page"/>

    <p>
        To differentiate between global "drivers" in a universe, and "drivers" in a season, I've decided to name drivers
        taking part in the season "racers".
    </p>

    <p>
        To add a racer to a season, you will first have to add an
        <InertiaLink :href="route('tutorials', 'season/entrants')">entrant</InertiaLink>
        and click the "drivers" link on the entrant overview page. The resulting page allows you to add any driver
        you've added to the current universe and is not currently active for another entrant in the same season. The
        selected driver number must also be still available in the current season.
    </p>

    <p>
        If you're adding a driver already used in another series or season, that driver's driver and reliability rating
        will be taken from the last season he received a rating. If no such ratings are available, both their driver and
        reliability rating will be 0.
    </p>

    <p>
        You can add an infinite amount of racers to an entrant, and can freely change an entrant's driver lineup
        throughout an active season.
    </p>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';</script>
