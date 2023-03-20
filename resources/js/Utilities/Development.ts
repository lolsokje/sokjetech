import { getRoll } from '@/Composables/useRunQualifying.js';
import DevelopmentState from '@/Interfaces/Development';
import { InertiaForm } from '@inertiajs/vue3';
import DevelopmentItem from '@/Interfaces/DevelopmentItem';

class Development {
    type?: string;

    constructor (type: string | undefined) {
        this.type = type;
    }

    performDev = (items: DevelopmentItem[]): boolean => {
        if (! this.areDevRangesValid(items)) {
            return false;
        }

        items.forEach((item: DevelopmentItem) => {
            this.performDevForItem(item);
        });

        return true;
    };

    performDevForItem = (item: DevelopmentItem): void => {
        const min = item.min;
        const max = item.max;

        const dev = getRoll(min, max);
        item.dev = dev;
        item.new = this.isReliability() ? item.reliability + dev : item.rating + dev;
    };

    areDevRangesValid = (items: DevelopmentItem[]): boolean => {
        return ! items.some((item: DevelopmentItem) => ! this.validateDevRange(item.min, item.max));
    };

    validateDevRange = (min: number, max: number): boolean => {
        return min <= max;
    };

    applyDevRangesToItems = (items: DevelopmentItem[], state: DevelopmentState): void => {
        items.forEach((item: DevelopmentItem) => {
            item.min = Math.round(state.min);
            item.max = Math.round(state.max);
        });
    };

    storeDev = (form: InertiaForm<any>, route: string, state: DevelopmentState): void => {
        form.post(route, {
            preserveState: true,
            onSuccess: () => state.completed = false,
        });
    };

    isReliability = (): boolean => {
        return this.type === 'reliability';
    };
}

export default Development;
