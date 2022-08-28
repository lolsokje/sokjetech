<template>
    <h3>Configuring a season</h3>

    <BackLink :backTo="route('tutorials', 'seasons')" label="season tutorials page"/>
    <BackLink :backTo="route('tutorials')" label="tutorial index page"/>

    <p>
        Aside from setting up a calendar and your teams, a season requires you to set up at least three other things;
    </p>

    <ol>
        <li>a point system</li>
        <li>a qualifying format</li>
        <li>reliability RNG and reasons</li>
    </ol>

    <p>
        <small>
            Be aware: once a season in started, none of these configurations can be changed. Make sure you've
            configured these three aspects to your liking before starting a new season
        </small>
    </p>

    <h5>Point system</h5>

    <p>
        To configure the point system, only the actual points-per-position are required. You define the amount of
        positions award points, and then set the amount of points awarded per position. Additionally you can select
        whether or not points are awarded for pole position and awarding the fastest lap, and the amount of points
        awarded for each. Finally, in case of the fastest lap, you can select whether the fastest lap will be taken from
        the final stint, or a separate stint. When rolling the fastest lap as a separate stint, a driver's rating will
        be included in the total roll.
    </p>

    <h5>Qualifying format</h5>

    <p>
        To add as much variety as possible, you get the option to select what qualifying format is used in the current
        season. In its current state, there's two different qualifying formats;
    </p>

    <ul>
        <li>Three Session Elimination qualifying, i.e. modern F1 qualifying</li>
        <li>Single Session qualifying, similar to F1's format one-hour shootout in use between 1996 and 2002</li>
    </ul>

    <p>
        With both formats you can configure the amount of runs performed each session, and the minimum and maximum RNG.
        With the Three Session Elimination format you'll also have to define how many drivers take part in the second
        and third session.
    </p>

    <h5>Reliability</h5>

    <p>
        There's three types of reliability in SokjeTech: engine, team and driver. Each of these three use the same RNG
        range defined in the settings, but you can define separate DNF reasons for each type. As of now they make no
        functional difference and only serve a cosmetic purpose. As for the actual reliability rolls, they're quite
        simple; if the roll is higher than an engine's, team's or driver's reliability rating, they'll retire.
    </p>

    <p>
        At this point there are no disqualifications.
    </p>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';</script>
