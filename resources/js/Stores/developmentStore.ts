import { reactive } from 'vue';
import { DevelopmentEntity } from '@/Interfaces/Season/Development/DevelopmentEntity';
import { InertiaForm } from '@inertiajs/vue3';
import DevelopmentTypes from '@/Constants/DevelopmentTypes';
import Type from '@/Interfaces/Season/Development/DevelopmentType';

type Form = {
    entities: DevelopmentEntity[],
}

type DevelopmentStore = {
    types: Type[],
    form: InertiaForm<Form>,
    selectedType: 'driver' | 'engine' | 'team',
    selectedComponent: string,
    min: number,
    max: number,
    hide_inputs: boolean,
    edit: boolean,
    completed: boolean,
    isDriver: boolean,
    isEngine: boolean,
    error: string | null,
};

export let developmentStore: DevelopmentStore = reactive({
    types: DevelopmentTypes,
    form: {},
    selectedType: 'driver',
    selectedComponent: 'rating',
    min: -5,
    max: 5,
    hide_inputs: false,
    edit: false,
    completed: false,
    isDriver: false,
    isEngine: false,
    error: null,
});
