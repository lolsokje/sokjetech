<template>
    <div class="row mb-3">
        <div class="col-6">
            <label for="race_type" class="form-label">Race type</label>
            <select id="race_type" v-model="raceType" required class="form-control">
                <option v-for="(type, value) in types" :key="type" :value="value">{{ type }}</option>
            </select>
        </div>

        <div class="col-6">
            <template v-if="raceType === RaceType.LAP">
                <label for="laps" class="form-label">Laps in race</label>
                <input id="laps"
                       type="number"
                       class="form-control"
                       v-model="laps"
                       :required="raceType === RaceType.LAP"
                       @input.prevent="emitDuration(laps)"
                >
            </template>
            <template v-if="raceType === RaceType.TIME">
                <div class="row">
                    <div class="col-6">
                        <label for="hours" class="form-label">Hours</label>
                        <input id="hours"
                               type="number"
                               class="form-control"
                               v-model="hours"
                               :required="raceType === RaceType.TIME"
                        >
                    </div>
                    <div class="col-6">
                        <label for="minutes" class="form-label">Minutes</label>
                        <input id="minutes"
                               type="number"
                               class="form-control"
                               v-model="minutes"
                               :required="raceType === RaceType.TIME"
                        >
                    </div>
                </div>
            </template>
            <template v-if="raceType === RaceType.DISTANCE">
                <label for="distance" class="form-label">Distance</label>
                <div class="input-group">
                    <input id="distance"
                           type="number"
                           class="form-control"
                           v-model="distance"
                           :required="raceType === RaceType.DISTANCE"
                    >
                    <select class="btn btn-outline-primary" v-model="distanceType">
                        <option :value="DistanceType.KM">kilometers</option>
                        <option :value="DistanceType.M">miles</option>
                    </select>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts" setup>
import RaceType from '@/Enums/RaceType';
import DistanceType from '@/Enums/DistanceType';
import { ref, Ref, watch } from 'vue';
import RaceDuration from '@/Interfaces/Race/RaceDuration';

interface Props {
    types: RaceType,
    race_type?: number,
    duration?: RaceDuration,
    distance_type?: string,
}

const props = defineProps<Props>();

const raceType: Ref<number> = ref(props.race_type ?? RaceType.LAP);
const distanceType: Ref<string> = ref(props.distance_type ?? DistanceType.KM);

const laps: Ref<number | null> = ref(props.duration?.raw ?? null);
const distance: Ref<number | null> = ref(props.duration?.distance ?? null);
const hours: Ref<number | null> = ref(props.duration?.hours ?? null);
const minutes: Ref<number | null> = ref(props.duration?.minutes ?? null);

const emit = defineEmits([ 'duration', 'raceType', 'distanceType' ]);

const emitDuration = (duration: number | null): void => {
    emit('duration', duration);
};

const calculateTotalMinutes = (): void => {
    emitDuration((hours.value * 60) + minutes.value);
};

const calculateRaceDistance = (): void => {
    let value = distance.value;
    if (distanceType.value === DistanceType.M) {
        value = convertMilesToKilometers(distance.value);
    }

    emitDuration(value * 1000);
};

const convertMilesToKilometers = (distance: number | null): number => {
    return distance * 1.609344;
};

watch(() => raceType.value, (): void => {
    hours.value = null;
    minutes.value = null;
    distance.value = null;
    laps.value = null;
    emitDuration(null);
    emit('raceType', raceType.value);
});

watch(hours, (): void => {
    calculateTotalMinutes();
});

watch(minutes, (): void => {
    calculateTotalMinutes();
});

watch(distance, (): void => {
    calculateRaceDistance();
});

watch(() => distanceType.value, (): void => {
    calculateRaceDistance();
    emit('distanceType', distanceType.value);
});
</script>
