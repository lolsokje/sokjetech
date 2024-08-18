<template>
    <ActiveRaceWarning v-if="has_active_race"/>
    <NoNextRaceWarning v-else-if="!has_next_race"/>
    <div class="row" v-else>
        <div class="col-2 border-end border-light">
            <h2>Settings</h2>

            <h4>Types</h4>
            <div v-for="type in types" :key="type">
                <p class="mb-2 mt-3">{{ uppercaseFirstLetter(type.name) }}</p>
                <div class="d-flex gap-2">
                    <span v-for="component in type.components"
                          :key="component"
                          class="component-button"
                          :class="{ 'active': selectedType === type.name && selectedComponent === component }"
                          @click.prevent="setComponent(type.name, component)"
                          :data-type="type.name"
                    >
                        {{ component }}
                    </span>
                </div>
            </div>

            <h4 class="mt-4">Configuration</h4>
            <div class="d-flex justify-content-between gap-3">
                <div>
                    <label for="min">Min</label>
                    <input type="number" id="min" class="form-control" v-model="developmentState.min">
                </div>
                <div>
                    <label for="max">Max</label>
                    <input type="number" id="max" class="form-control" v-model="developmentState.max">
                </div>
            </div>

            <button class="btn btn-outline-warning mt-3" @click.prevent="applyRanges()">Apply</button>

            <div class="mt-3">
                <div>
                    <input type="checkbox"
                           id="hide_inputs"
                           class="form-check-inline"
                           v-model="developmentState.hide_inputs"
                    >
                    <label for="hide_inputs" class="form-check-label">Hide inputs</label>
                </div>

                <div>
                    <input type="checkbox"
                           id="edit"
                           class="form-check-inline"
                           v-model="developmentState.edit"
                           :disabled="!developmentState.completed"
                    >
                    <label for="edit" class="form-check-label">Edit ratings directly</label>
                    <p v-if="!developmentState.completed">
                        <small>
                            Run dev first before editing individual ratings
                        </small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-10 px-5">
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <h2 class="text-center">
                    Development |
                    {{ uppercaseFirstLetter(selectedType) }} |
                    {{ uppercaseFirstLetter(selectedComponent) }}
                </h2>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary"
                            :disabled="developmentState.completed"
                            @click.prevent="runDevelopment()"
                    >
                        Run
                    </button>
                    <button class="btn btn-success"
                            :disabled="!developmentState.completed"
                            @click.prevent="saveDevelopment()"
                    >
                        Save
                    </button>
                </div>
            </div>

            <table class="table" id="screenshot-target">
                <thead>
                <tr>
                    <th v-if="!isEngine" class="colour-accent"></th>
                    <th>Name</th>
                    <template v-if="isDriver">
                        <th class="text-center">#</th>
                        <th>Team</th>
                        <th class="text-center">Age</th>
                    </template>
                    <template v-if="isEngine">
                        <th class="text-center">Rebadged</th>
                        <th class="text-center">Individual rating</th>
                    </template>
                    <th>Current</th>
                    <template v-if="!developmentState.hide_inputs">
                        <th class="text-center">Min</th>
                        <th class="text-center">Max</th>
                    </template>
                    <th class="text-center">Dev</th>
                    <th class="text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="entity in form.entities" :key="entity.id">
                    <BackgroundColourCell v-if="!isEngine" :backgroundColour="entity.extra.accent"/>
                    <td class="padded-left">{{ entity.label }}</td>
                    <template v-if="isDriver">
                        <DriverNumberCell :number="entity.extra.number" :styleString="entity.styleString"/>
                        <td class="padded-left align-middle">{{ entity.extra.team }}</td>
                        <td class="text-center">{{ entity.extra.age }}</td>
                    </template>
                    <template v-if="isEngine">
                        <td class="big-centered">
                            <fa icon="check" class="text-success" size="lg" v-if="entity.extra.rebadged"/>
                            <fa icon="times" class="text-danger" size="lg" v-else/>
                        </td>
                        <td class="big-centered">
                            <template v-if="entity.extra.rebadged">
                                <fa icon="check" class="text-success" size="lg" v-if="entity.extra.individual_rating"/>
                                <fa icon="times" class="text-danger" size="lg" v-else/>
                            </template>
                        </td>
                    </template>
                    <td class="small-centered bg-accent-odd">{{ entity.current }}</td>
                    <template v-if="isEngine && entity.extra.rebadged && !entity.extra.individual_rating && !developmentState.hide_inputs">
                        <td></td>
                        <td></td>
                    </template>
                    <template v-else-if="!developmentState.hide_inputs">
                        <td class="medium-centered">
                            <input type="number" class="form-control text-center" v-model="entity.min">
                        </td>
                        <td class="medium-centered">
                            <input type="number" class="form-control text-center" v-model="entity.max">
                        </td>
                    </template>
                    <td class="small-centered">
                        {{ entity.rng }}
                    </td>
                    <td class="small-centered bg-accent-even" :class="{'medium-centered': developmentState.edit }">
                        <template v-if="developmentState.edit && showEngineInput(entity)">
                            <input type="number"
                                   class="form-control text-center"
                                   v-model="entity.new"
                                   @change.prevent="checkRebadgedRating(entity)"
                            >
                        </template>
                        <span v-else-if="entity.new">
                            {{ entity.new }}
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>

            <CopyScreenshotButton/>
        </div>
    </div>
</template>

<script lang="ts" setup>
import SeasonInterface from '@/Interfaces/Season';
import { computed, ComputedRef, reactive, ref, Ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { uppercaseFirstLetter } from '@/Support/String';
import { getRoll } from '@/Composables/useRandom';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import NoNextRaceWarning from '@/Shared/NoNextRaceWarning.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';

type DevelopmentEntity = {
    id: string,
    label: string,
    current: number,
    extra: {
        accent: string,
        number: number,
        team: string,
        age: number,
        rebadged: boolean,
        individual_rating: boolean,
        base_engine_id: string,
    },
    styleString: string,
    min: number,
    max: number,
    rng: number,
    new: number,
}

type Props = {
    season: SeasonInterface,
    entities: DevelopmentEntity[],
    type: 'driver' | 'team' | 'engine',
    component: string,
    has_active_race: boolean,
    has_next_race: boolean,
};

type Form = {
    entities: DevelopmentEntity[],
}

type DevelopmentState = {
    min: number,
    max: number,
    hide_inputs: boolean,
    edit: boolean,
    completed: boolean,
}

const props = defineProps<Props>();

const developmentState: DevelopmentState = reactive({
    min: 0,
    max: 30,
    hide_inputs: false,
    edit: false,
    completed: false,
});

const selectedType: Ref<string> = ref(props.type);
const selectedComponent: Ref<string> = ref(props.component);

const isDriver: ComputedRef<boolean> = computed(() => selectedType.value === 'driver');
const isEngine: ComputedRef<boolean> = computed(() => selectedType.value === 'engine');

const showEngineInput = (entity: DevelopmentEntity): boolean => {
    if (! isEngine.value) {
        return false;
    }

    return ! entity.extra.rebadged || entity.extra.rebadged && entity.extra.individual_rating;
};

const types = [
    {
        name: 'driver',
        components: [ 'rating', 'reliability' ],
    },
    {
        name: 'team',
        components: [ 'rating', 'reliability' ],
    },
    {
        name: 'engine',
        components: [ 'rating', 'reliability' ],
    },
];

const setComponent = (type: string, component: string): void => {
    router.get(route('seasons.development.show', { season: props.season, type, component }));
};

const form = useForm<Form>({
    entities: props.entities,
});

const applyRanges = (): void => {
    form.entities.forEach(entity => {
        entity.min = developmentState.min;
        entity.max = developmentState.max;
    });
};

const runDevelopment = (): void => {
    form.entities.forEach(entity => {
        entity.rng = getRoll(entity.min, entity.max);
        entity.new = entity.current + entity.rng;
    });

    if (isEngine.value) {
        const rebadgedEnginesWithIndividualRating = form.entities.filter(entity => entity.extra.rebadged && ! entity.extra.individual_rating);

        rebadgedEnginesWithIndividualRating.forEach(engine => {
            const parentEngine = form.entities.find(e => e.extra.base_engine_id === engine.extra.base_engine_id && ! e.extra.rebadged);

            if (! parentEngine) {
                return;
            }

            engine.rng = parentEngine.rng;
            engine.new = parentEngine.new;
        });
    }

    developmentState.completed = true;
};

const checkRebadgedRating = (engine: DevelopmentEntity): void => {
    if (! isEngine.value) {
        return;
    }

    const rebadgedEngines = form.entities.filter(e => e.extra.base_engine_id === engine.extra.base_engine_id && e.extra.rebadged && ! e.extra.individual_rating);

    rebadgedEngines.forEach(e => e.new = engine.new);
};

const saveDevelopment = (): void => {
    form.post(route('seasons.development.store', {
        season: props.season,
        type: selectedType.value,
        component: selectedComponent.value,
    }));
};
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>

<style scoped lang="scss">
$primary: var(--primary);

.component-button {
    background-color: $primary;
    border-radius: 0.25rem;
    padding: 0.2rem 0.5rem;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;

    &:hover {
        background-color: var(--base-100);
        outline: 1px solid var(--primary);
    }

    &.active {
        background-color: var(--base-100);
        outline: 2px solid var(--secondary);
    }
}
</style>
