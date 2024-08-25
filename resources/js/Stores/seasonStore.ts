import Season from '@/Interfaces/Season';
import { reactive } from 'vue';

type Store = {
    season: Season | null,
};

export let seasonStore: Store = reactive({
    season: null,
});
