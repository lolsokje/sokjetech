<template>
    <div class="row">
        <div class="col-2 border-end border-light">
            <DevelopmentTypeSelect/>
        </div>

        <div class="col-10 px-5">
            <Line :data="data" :options="options"/>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { Line } from 'vue-chartjs';
import {
    CategoryScale,
    Chart as ChartJS,
    Colors,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import DevelopmentTypeSelect from '@/Components/Season/Development/DevelopmentTypeSelect.vue';
import { computed, ComputedRef, onMounted } from 'vue';
import { themeStore } from '@/Stores/themeStore';
import { developmentStore } from '@/Stores/developmentStore';

ChartJS.register(
    Colors,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
);

type Props = {
    results: {
        label: string,
        history: number[],
        primary: string,
        secondary: string,
        accent: string,
        dash: boolean,
    }[],
    races: {
        race: string,
        country: string,
    }[],
    type: 'driver' | 'team' | 'engine',
    component: string,
}

const props = defineProps<Props>();

const labels = props.races.map(race => race.race);

const datasets = props.results.map(result => {
    const backgroundColour = result.accent === result.primary ? result.secondary : result.primary;

    return {
        label: result.label,
        data: result.history,
        borderColor: result.accent,
        backgroundColor: backgroundColour,
        pointRadius: 0,
        borderDash: result.dash ? [ 5, 10 ] : [],
    };
});

const data = { labels, datasets };

const min = Math.min(...datasets.map(dataset => Math.min(...dataset.data.filter(v => v !== 0 && v !== null))));
const max = Math.max(...datasets.map(dataset => Math.max(...dataset.data)));

const style: ComputedRef<CSSStyleDeclaration> = computed(() => getComputedStyle(document.body));

const scaleOptions = computed(() => {
    return {
        grid: {
            color: themeStore.base300,
        },
        ticks: {
            color: themeStore.color,
        },
    };
});

const options = computed(() => {
    return {
        scales: {
            x: scaleOptions.value,
            y: {
                ...scaleOptions.value,
                min: Math.floor(min / 5) * 5,
                max: Math.ceil(max / 5) * 5,
            },
        },
        color: themeStore.color,
        interaction: {
            intersect: false,
        },
    };
});

onMounted(() => {
    developmentStore.selectedType = props.type;
    developmentStore.selectedComponent = props.component;
});
</script>

<script lang="ts">
import Season from '@/Layouts/Season.vue';


export default { layout: Season };
</script>
