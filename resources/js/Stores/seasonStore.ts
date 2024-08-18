import Season from '@/Interfaces/Season';
import { reactive } from 'vue';

type Store = {
    season: Season,
};

export let seasonStore: Store = reactive({
    season: {},
});
