<template>
    <Breadcrumb :link="route('series.seasons.index', season.series)"
                :linkText="season.series.name"
                :label="season.full_name"
                append="Calendar"
    />

    <div class="my-4" v-if="canEdit && !season.started">
        <div class="d-flex">
            <button class="btn btn-success" @click.prevent="confirmSeasonStart()" :disabled="!canStart">
                Start season
            </button>
            <InertiaLink :href="route('seasons.settings.copy.index', [season])" class="btn btn-primary ms-3">
                Copy season setup
            </InertiaLink>
        </div>
        <div class="text-danger mt-2" v-if="!canStart">
            You need to configure the qualifying format, points system and add at least one race before you can
            start the season
        </div>
    </div>
    <div class="my-4" v-if="canComplete">
        <button class="btn btn-success" @click.prevent="confirmSeasonComplete()">Complete season</button>
    </div>

    <!--    <BackLink :backTo="route('series.seasons.index', [season.series])" label="season index"/>-->

    <!--    <h3>Races</h3>-->

    <InertiaLink v-if="canAddRace()" :href="route('seasons.races.create', [season])" class="btn btn-primary my-3">
        Add race
    </InertiaLink>

    <InertiaLink
        v-if="canReorderRaces()" :href="route('seasons.races.reorder', [season])"
        class="btn btn-primary my-3 ms-3"
    >
        Reorder races
    </InertiaLink>

    <div class="mb-3">
        <div>
            <div class="form-check-inline">
                <input id="edit-mode" v-model="spoilerFree" class="form-check-inline" type="checkbox">
                <label class="form-check-label" for="edit-mode">Hide results</label>
            </div>
        </div>
    </div>

    <table class="table" id="screenshot-target">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th></th>
            <th>Name</th>
            <template v-if="!spoilerFree">
                <th colspan="2">Pole</th>
                <th colspan="2">Winning driver</th>
                <th colspan="2">Winning team</th>
            </template>
            <template v-else>
                <th colspan="2"></th>
                <th colspan="2"></th>
                <th colspan="2"></th>
            </template>
            <th :colspan="!season.started && canEdit ? 2 : 1"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="race in races" :key="race.id">
            <td class="smallest-centered">{{ race.order }}</td>
            <td class="smallest-centered">
                <CountryFlag :country="race.circuit.country"/>
            </td>
            <td class="padded-left">{{ race.name }}</td>
            <template v-if="!spoilerFree && race.completed">
                <td class="smallest-centered" :style="race.pole?.style_string">{{ race.pole?.number }}</td>
                <td class="padded-left">
                    {{ race.pole?.full_name }}
                </td>
                <td class="smallest-centered" :style="race.winner?.style_string">{{ race.winner?.number }}</td>
                <td class="padded-left">{{ race.winner?.full_name }}</td>
                <BackgroundColourCell :backgroundColour="race.winner?.background_colour"/>
                <td class="padded-left">{{ race.winner?.team_name }}</td>
            </template>
            <template v-else>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </template>
            <td class="small-centered">
                <InertiaLink :href="getRaceLink(race)">{{ getRaceLinkText(race) }}</InertiaLink>
            </td>
            <td class="medium-centered" v-if="!season.started && canEdit">
                <button class="btn btn-link" @click.prevent="deleteRace(race)">
                    delete
                </button>
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup lang="ts">
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { computed, onMounted, Ref, ref, watch } from 'vue';
import axios from 'axios';
import { Participant, Race } from '@/Interfaces/Race';
import SeasonInterface from '@/Interfaces/Season';
import { router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

interface Props {
    season: SeasonInterface,
    poles: Participant[],
    winners: Participant[],
    next_race_id?: string,
    can: Permission,
}

const props = defineProps<Props>();

const canEdit = props.can.edit;
const spoilerFree = ref(true);
const races: Ref<Race[] | undefined> = ref([]);

const stages: { [key: string]: string } = {
    INTRO: 'intro',
    QUALIFYING: 'qualifying',
    GRID: 'grid',
    RACE: 'race',
    RESULTS: 'results',
};

const deleteRace = async (race: Race) => {
    if (! confirm("Are you sure you want to delete this race from the calendar?")) {
        return;
    }

    await axios.delete(route('seasons.races.destroy', [ props.season, race ]))
        .catch();

    resetRacesAfterDeletion(race);
};

const resetRacesAfterDeletion = (race: Race): void => {
    races.value = races.value?.filter((r: Race) => r.id !== race.id);
    races.value?.forEach((race, index) => race.order = index + 1);
};

const canRunRaces = (): boolean => {
    return <boolean>canEdit;
};

const canAddRace = (): boolean => {
    return <boolean>canEdit && ! props.season.started;
};

const canReorderRaces = (): boolean => {
    return canAddRace() && props.season.races?.length > 1;
};

const isNextRace = (race: Race): boolean => {
    return race.id === props.next_race_id;
};

const getCurrentRaceStage = (race: Race): string => {
    if (race.completed) {
        return stages.RESULTS;
    }

    if (! race.qualifying_started) {
        return stages.INTRO;
    }

    if (race.qualifying_started && ! race.qualifying_completed) {
        return stages.QUALIFYING;
    }

    if (race.qualifying_completed && ! race.started) {
        return stages.GRID;
    }

    if (race.started && ! race.completed) {
        return stages.RACE;
    }
};

const getRaceLink = (race: Race): string => {
    if (canRunRaces() && ! props.season.started) {
        return route('seasons.races.edit', [ props.season, race ]);
    }

    const currentStage = getCurrentRaceStage(race);
    if (currentStage === stages.RESULTS) {
        return route('weekend.results', [ race ]);
    }

    if (! isNextRace(race)) {
        return 'null';
    }

    if (currentStage === stages.INTRO) {
        return route('weekend.intro', [ race ]);
    }

    if (currentStage === stages.QUALIFYING) {
        return route('weekend.qualifying', [ race ]);
    }

    if (currentStage === stages.GRID) {
        return route('weekend.grid', [ race ]);
    }

    if (currentStage === stages.RACE) {
        return route('weekend.race', [ race ]);
    }
    return route('weekend.results', [ race ]);
};

const getRaceLinkText = (race: Race): string | null => {
    const canRunRace = canRunRaces();
    if (canRunRace && ! props.season.started) {
        return 'edit';
    }

    const currentStage = getCurrentRaceStage(race);

    if (currentStage === stages.RESULTS) {
        return 'results';
    }

    if (! isNextRace(race)) {
        return null;
    }

    if (props.season.started && currentStage === stages.INTRO) {
        return canRunRace ? 'start' : 'preview';
    }

    if (currentStage === stages.GRID) {
        return 'grid';
    }

    if (currentStage === stages.QUALIFYING || currentStage === stages.RACE) {
        return canRunRace ? 'continue' : 'view';
    }
};

const attachPoleAndWinner = (race: Race): void => {
    const pole = props.poles.find(pole => pole.race_id === race.id);
    const winner = props.winners.find(winner => winner.race_id === race.id);

    race.pole = pole;
    race.winner = winner;
};

const confirmSeasonStart = (): void => {
    if (! confirm('Are you sure you want to start the season? You will no longer be able to modify the calendar, qualifying format and point system')) {
        return;
    }

    router.put(route('seasons.start', [ props.season ]));
};

const confirmSeasonComplete = (): void => {
    if (! confirm("Are you sure you want to mark the current season as completed?")) {
        return;
    }

    router.put(route('seasons.complete', [ props.season ]));
};

const canStart = computed(() => props.season.can_start && canEdit);
const canComplete = computed(() => props.season.can_complete && canEdit);

watch(spoilerFree, (value) => {
    localStorage.setItem('spoiler_free', value.toString());
});

onMounted(() => {
    races.value = props.season?.races;
    races.value?.forEach(race => {
        attachPoleAndWinner(race);
    });

    races.value?.sort((raceOne: Race, raceTwo: Race): number => raceOne.order - raceTwo.order);

    if (localStorage.getItem('spoiler_free')) {
        spoilerFree.value = localStorage.getItem('spoiler_free') === 'true';
    }
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
