<template>
    <div class="progress rounded-0 rounded-bottom" style="height:30px">
        <div :class="`progress-bar ${event.flag}-flag`"
             v-for="(event, key) in flagEventStore.flags"
             :key="key"
             :style="getProgressWidth(event)"
        >
            <!--            <small>-->
            <!--                <p class="m-0">{{ event.startLap }}-->
            <!--                    <span v-if="event.endLap">- {{ event.endLap }}</span>-->
            <!--                    <span v-else>- {{ raceStateStore.currentLap }}</span>-->
            <!--                </p>-->
            <!--                <p class="m-0">{{ event.type }}</p>-->
            <!--            </small>-->
        </div>
    </div>
</template>

<script lang="ts" setup>
import { flagEventStore, RaceFlagEvent } from '@/Stores/Race/flagEventStore';
import { raceStateStore } from '@/Stores/raceStateStore';

const getProgressWidth = (event: RaceFlagEvent): string => {
    const endLap = event.endLap ?? raceStateStore.lapDetails.duration;

    const percentage = ((endLap - event.startLap) / raceStateStore.lapDetails.duration) * 100;

    return `width: ${percentage}%`;
};
</script>
