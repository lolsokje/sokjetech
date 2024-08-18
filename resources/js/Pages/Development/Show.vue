<template>
    <ActiveRaceWarning v-if="has_active_race"/>
    <NoNextRaceWarning v-else-if="!has_next_race"/>

    <div class="row" v-else>
        <div class="col-2 border-end border-light">
            <DevelopmentSettings/>
        </div>

        <div class="col-10 px-5">
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <DevelopmentHeader/>

                <DevelopmentButtons/>
            </div>

            <div class="alert bg-danger w-50 mx-auto text-center" v-if="developmentStore.error">
                {{ developmentStore.error }}
            </div>

            <table class="table" id="screenshot-target">
                <thead>
                <tr>
                    <th v-if="!developmentStore.isEngine" class="colour-accent"></th>
                    <th>Name</th>
                    <template v-if="developmentStore.isDriver">
                        <th class="text-center">#</th>
                        <th>Team</th>
                        <th class="text-center">Age</th>
                    </template>
                    <template v-if="developmentStore.isEngine">
                        <th class="text-center">Rebadged</th>
                        <th class="text-center">Individual rating</th>
                    </template>
                    <th>Current</th>
                    <template v-if="!developmentStore.hide_inputs">
                        <th class="text-center">Min</th>
                        <th class="text-center">Max</th>
                    </template>
                    <th class="text-center">Dev</th>
                    <th class="text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="entity in developmentStore.form.entities" :key="entity.id">
                    <BackgroundColourCell v-if="!developmentStore.isEngine" :backgroundColour="entity.extra.accent"/>
                    <td class="padded-left">{{ entity.label }}</td>
                    <template v-if="developmentStore.isDriver">
                        <DriverNumberCell :number="entity.extra.number" :styleString="entity.styleString"/>
                        <td class="padded-left align-middle">{{ entity.extra.team }}</td>
                        <td class="text-center">{{ entity.extra.age }}</td>
                    </template>
                    <template v-if="developmentStore.isEngine">
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
                    <template v-if="developmentStore.isEngine && entity.extra.rebadged && !entity.extra.individual_rating && !developmentStore.hide_inputs">
                        <td></td>
                        <td></td>
                    </template>
                    <template v-else-if="!developmentStore.hide_inputs">
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
                    <td class="small-centered bg-accent-even" :class="{'medium-centered': developmentStore.edit }">
                        <template v-if="developmentStore.edit && showRatingInput(entity)">
                            <input type="number"
                                   class="form-control text-center"
                                   v-model="entity.new"
                                   @change.prevent="applyManualRating(entity)"
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
import { onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import ActiveRaceWarning from '@/Shared/ActiveRaceWarning.vue';
import NoNextRaceWarning from '@/Shared/NoNextRaceWarning.vue';
import DriverNumberCell from '@/Components/DriverNumberCell.vue';
import CopyScreenshotButton from '@/Shared/CopyScreenshotButton.vue';
import DevelopmentSettings from '@/Components/Season/Development/DevelopmentSettings.vue';
import { developmentStore } from '@/Stores/developmentStore';
import { DevelopmentEntity } from '@/Interfaces/Season/Development/DevelopmentEntity';
import { seasonStore } from '@/Stores/seasonStore';
import DevelopmentHeader from '@/Components/Season/Development/DevelopmentHeader.vue';
import DevelopmentButtons from '@/Components/Season/Development/DevelopmentButtons.vue';

type Props = {
    season: SeasonInterface,
    entities: DevelopmentEntity[],
    type: 'driver' | 'team' | 'engine',
    component: string,
    has_active_race: boolean,
    has_next_race: boolean,
};

const props = defineProps<Props>();

const showRatingInput = (entity: DevelopmentEntity): boolean => {
    if (! developmentStore.isEngine) {
        return true;
    }

    // don't show the input if the engine isn't rebadged, or if it is rebadged and has an individual rating
    return ! entity.extra.rebadged || entity.extra.rebadged && entity.extra.individual_rating;
};

const applyManualRating = (entity: DevelopmentEntity): void => {
    // ensure the rolled RNG is properly updated, otherwise the histories table records the wrong dev amount
    entity.rng = entity.new - entity.current;

    if (! developmentStore.isEngine) {
        return;
    }

    applyRebadgedEngineRatings(entity);
};

const applyRebadgedEngineRatings = (entity: DevelopmentEntity): void => {
    // Rebadged engines without individual rating take the rating from their "parent" engine, so make sure both the new rating and RNG are taken from said parent engine
    const rebadgedEngines = developmentStore.form.entities.filter(e => e.extra.base_engine_id === entity.extra.base_engine_id && e.extra.rebadged && ! e.extra.individual_rating);

    rebadgedEngines.forEach(e => {
        e.new = entity.new;
        e.rng = entity.rng;
    });
};

onMounted(() => {
    developmentStore.form = useForm({
        entities: props.entities,
    });
    developmentStore.selectedType = props.type;
    developmentStore.selectedComponent = props.component;
    developmentStore.isDriver = developmentStore.selectedType === 'driver';
    developmentStore.isEngine = developmentStore.selectedType === 'engine';

    seasonStore.season = props.season;
});

onUnmounted(() => {
    developmentStore.completed = false;
    developmentStore.form.entities = [];
    developmentStore.error = null;
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';

export default { layout: Season };
</script>
