<template>
    <BackLink :backTo="route('series.seasons.index', [season.series])" label="series overview"/>

    <h3>Races</h3>

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
            <th>Name</th>
            <template v-if="!spoilerFree">
                <th>Pole</th>
                <th>Winning driver</th>
                <th colspan="2">Winning team</th>
            </template>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="race in season.races" :key="race.id">
            <td class="small-centered">{{ race.order }}</td>
            <td class="padded-left">{{ race.name }}</td>
            <template v-if="!spoilerFree">
                <td class="padded-left">
                    <template v-if="race.qualifying_completed">
                        {{ race.pole?.full_name }}
                    </template>
                </td>
                <td class="padded-left">
                    <template v-if="race.completed">
                        {{ race.winner?.full_name }}
                    </template>
                </td>
                <td class="colour-accent"
                    :style="race.completed ? `background-color: ${race.winner?.background_colour}` : ''"></td>
                <td class="padded-left">
                    <template v-if="race.completed">
                        {{ race.winner?.team_name }}
                    </template>
                </td>
            </template>
            <td class="small-centered">
                <InertiaLink :href="getRaceLink(race)">{{ getRaceLinkText(race) }}</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
    <CopyScreenshotButton/>
</template>

<script setup>
import BackLink from '@/Shared/BackLink';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton';
import { onMounted, ref, watch } from 'vue';

const spoilerFree = ref(true);

const props = defineProps({
    season: {
        type: Object,
        required: true,
    },
    poles: Array,
    winners: Array,
    next_race_id: {
        type: String,
        required: false,
    },
    can: {
        type: Object,
        required: true,
    },
});

const stages = {
    INTRO: 'intro',
    QUALIFYING: 'qualifying',
    GRID: 'grid',
    RACE: 'race',
    RESULTS: 'results',
};

const canRunRaces = () => {
    return props.can.edit;
};

const canAddRace = () => {
    return props.can.edit && !props.season.started;
};

const canReorderRaces = () => {
    return canAddRace() && props.season.races.length > 1;
};

const canEditRace = (race) => {
    return canAddRace() && !race.completed;
};

const isNextRace = (race) => {
    return race.id === props.next_race_id;
};

const getCurrentRaceStage = (race) => {
    if (race.completed) {
        return stages.RESULTS;
    }

    if (!race.qualifying_started) {
        return stages.INTRO;
    }

    if (race.qualifying_started && !race.qualifying_completed) {
        return stages.QUALIFYING;
    }

    if (race.qualifying_completed && !race.started) {
        return stages.GRID;
    }

    if (race.started && !race.completed) {
        return stages.RACE;
    }
};

const getRaceLink = (race) => {
    if (canRunRaces() && !props.season.started) {
        return route('seasons.races.edit', [ props.season, race ]);
    }

    const currentStage = getCurrentRaceStage(race);
    if (currentStage === stages.RESULTS) {
        return route('weekend.results', [ race ]);
    }

    if (!isNextRace(race)) {
        return null;
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

const getRaceLinkText = (race) => {
    const canRunRace = canRunRaces();
    if (canRunRace && !props.season.started) {
        return 'edit';
    }

    const currentStage = getCurrentRaceStage(race);

    if (currentStage === stages.RESULTS) {
        return 'results';
    }

    if (!isNextRace(race)) {
        return null;
    }

    if (currentStage === stages.INTRO) {
        return canRunRace ? 'start' : 'preview';
    }

    if (currentStage === stages.GRID) {
        return 'grid';
    }

    if (currentStage === stages.QUALIFYING || currentStage === stages.RACE) {
        return canRunRace ? 'continue' : 'view';
    }
};

const attachPoleAndWinner = (race) => {
    const pole = props.poles.find(pole => pole.race_id === race.id);
    const winner = props.winners.find(winner => winner.race_id === race.id);

    if (pole) {
        race.pole = pole;
    }

    if (winner) {
        race.winner = winner;
    }
};

watch(spoilerFree, (value) => {
    localStorage.setItem('spoiler_free', value.toString());
});

onMounted(() => {
    props.season.races.forEach(race => {
        attachPoleAndWinner(race);
    });

    if (localStorage.getItem('spoiler_free')) {
        spoilerFree.value = localStorage.getItem('spoiler_free') === 'true';
    }
});
</script>

<script>
import Season from '@/Layouts/Season';

export default { layout: Season };
</script>
