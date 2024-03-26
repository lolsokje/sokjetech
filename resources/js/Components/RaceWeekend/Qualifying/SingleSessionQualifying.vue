<template>
    <div class="d-flex my-3">
        <div class="ms-auto" v-if="qualifyingStore.canRunQualifying">
            <button @click.prevent="performRun()"
                    class="btn btn-primary"
                    :disabled="qualifyingStore.saving || qualifyingStore.error"
                    v-if="canPerformRun()"
            >
                Perform run
            </button>
            <button v-if="canCompleteQualifying()"
                    class="btn btn-success"
                    @click.prevent="completeQualifying()"
                    :disabled="qualifyingStore.saving || qualifyingStore.error"
            >
                Complete qualifying
            </button>
        </div>
    </div>
    <div id="screenshot-target">
        <div class="race-details">
            <h2>Round {{ qualifyingStore.race.order }} - {{ qualifyingStore.race.name }}</h2>
            <h2 class="ms-auto">Qualifying</h2>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Pos</th>
                <th class="colour-accent"></th>
                <th>Driver</th>
                <th class="text-center">#</th>
                <th>Team</th>
                <th class="text-center">Rating</th>
                <th v-for="i in qualifyingStore.formatDetails.runs_per_session" :key="i" class="text-center">
                    {{ i }}
                </th>
                <th class="text-center">Best</th>
                <th class="text-center">Total</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(result, position) in qualifyingStore.results" :key="result.id">
                <td class="smallest-centered">
                    {{ result.performance.sessions[qualifyingStore.currentSession - 1]?.position ?? position + 1 }}
                </td>
                <BackgroundColourCell :backgroundColour="result.team.accent_colour"/>
                <td>
                    <DriverName :firstName="result.driver.first_name" :lastName="result.driver.last_name"/>
                </td>
                <DriverNumberCell :number="result.driver.number" :styleString="result.team.style_string"/>
                <td class="padded-left">{{ result.team.name }}</td>
                <td class="small-centered bg-accent-odd">{{ result.ratings.total }}</td>
                <td v-for="i in qualifyingStore.formatDetails.runs_per_session"
                    :key="i"
                    class="small-centered"
                    :class="{ 'bg-accent-even': isEven(i) }"
                >
                    {{ result.performance.sessions[qualifyingStore.currentSession - 1].runs[i - 1] }}
                </td>
                <td class="small-centered">{{ result.performance.best_stint }}</td>
                <td class="small-centered bg-accent-odd">{{ result.performance.total }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts" setup>
import { qualifyingStore } from '@/Stores/qualifyingStore';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import DriverName from '@/Components/DriverName.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import { isEven } from '@/Utilities/IsEven';
import { canCompleteQualifying, canPerformRun, completeQualifying, performRun } from '@/Composables/useQualifying';
</script>
