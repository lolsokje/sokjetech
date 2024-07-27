<template>
    <div class="race-progress-wrapper">
        <div class="race-progress bg-success" :style="`width: ${progress}%`"></div>

        <span class="race-event"
              v-for="(event, index) in events"
              :key="index"
              :style="`left: calc(${offset(event.lap)}% - 3px)`"
        >
            <fa :icon="icon(event.type)" class="race-event-icon" :class="color(event.type)" size="lg"/>
        </span>
    </div>
</template>

<script lang="ts" setup>
import RaceEvent from '@/Interfaces/RaceWeekend/RaceEvent';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';
import { computed } from 'vue';
import { RaceEventType } from '@/Interfaces/RaceWeekend/RaceEventType';
import { percentage } from '@/Support/Math';

interface Props {
    progress: number,
    events?: RaceEvent[],
}

defineProps<Props>();

const duration = computed(() => raceWeekendStore.race?.duration ?? 1);

const offset = (lap: number): number => {
    return percentage(lap - 1, duration.value);
};

const icon = (type: RaceEventType): string => {
    if (type === RaceEventType.MISTAKE) {
        return 'exclamation-circle';
    }

    return 'car-crash';
};

const color = (type: RaceEventType): string => {
    if (type === RaceEventType.MISTAKE) {
        return 'text-warning';
    }

    return 'text-danger';
};
</script>

<style lang="scss" scoped>
.race-progress-wrapper {
    position: relative;
    width: 100%;
    height: 10px;
    background-color: #CCCCCC;
    border-radius: 0.5rem;

    .race-progress:first-child {
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
    }

    .race-progress:last-child {
        border-top-right-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .race-progress {
        height: 10px;
    }

    .race-event {
        position: absolute;
        top: calc(50% - 12px);

        .race-event-icon {
            background-color: var(--table-bg-body);
        }
    }
}
</style>
