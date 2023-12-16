<template>
    <div class="d-flex mb-3">
        <div class="ms-auto" v-if="can.edit">
            <button v-if="canSimNextLap"
                    class="btn btn-primary"
                    @click.prevent="emit('simLap')"
                    :disabled="raceStateStore.saving"
            >
                Sim next lap
            </button>
            <button v-if="canSimNextLap"
                    class="btn btn-secondary ms-3"
                    :disabled="raceStateStore.saving"
                    @click.prevent="emit('simRace')"
            >
                Sim full race
            </button>
            <button v-if="canCompleteRace"
                    class="btn btn-success"
                    @click.prevent="completeRace(raceStateStore.race)"
                    :disabled="raceStateStore.saving"
            >
                Complete race
            </button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { completeRace } from '@/Composables/useRace';
import { computed, ComputedRef } from 'vue';
import { raceStateStore } from '@/Stores/raceStateStore';
import Permission from '@/Interfaces/Permission';

interface Props {
    can: Permission,
}

const props = defineProps<Props>();

const simNextLapCheck = (): boolean => {
    return raceStateStore.currentLap < raceStateStore.lapDetails.duration && ! raceStateStore.showError;
};

const raceCompletionCheck = (): boolean => {
    if (raceStateStore.race === null) {
        return false;
    }

    if (raceStateStore.completed) {
        return false;
    }
};

const emit = defineEmits([ 'simLap', 'simRace' ]);

const canSimNextLap: ComputedRef<boolean> = computed(() => simNextLapCheck());
const canCompleteRace: ComputedRef<boolean> = computed(() => raceCompletionCheck());
</script>
