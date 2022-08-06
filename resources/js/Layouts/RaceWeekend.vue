<template>
    <Base>
        <div class="pb-3">
            <TabLinks :links="links"/>
        </div>

        <div class="w-100 bg-dark p-4">
            <h1>{{ race.name }}</h1>
            <slot/>
        </div>
    </Base>
</template>

<script setup>
import Base from '@/Layouts/Base';
import TabLinks from '@/Components/TabLinks';
import { computed, onMounted } from 'vue';
import { raceWeekendStore } from '@/Stores/raceWeekendStore';

const props = defineProps({
    race: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    raceWeekendStore.initialiseLinks(props.race);
    links.value = raceWeekendStore.getLinks();
});

const links = computed(() => raceWeekendStore.getLinks());
</script>

<script>
export default { name: 'RaceWeekendLayout' };
</script>
