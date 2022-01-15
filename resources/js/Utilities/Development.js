class Development {
    performDev (items) {
        if (this.areDevRangesValid(items)) {
            items.forEach((item) => {
                this.performDevForItem(item);
            });
            return true;
        }
        return false;
    }

    performDevForItem (item) {
        const min = item.min;
        const max = item.max;

        const dev = Math.round(Math.random() * (max - min) + min);
        item.dev = dev;
        item.new = item.rating + dev;
    }

    areDevRangesValid (items) {
        return !items.some((item) => !this.validateDevRange(item));
    }

    validateDevRange (item) {
        return item.min <= item.max;
    }

    applyDevRangesToItems (items, state) {
        items.forEach((item) => {
            item.min = Math.round(state.min);
            item.max = Math.round(state.max);
        });
    }

    storeDev (form, route, state) {
        form.post(route, {
            preserveState: true,
            onSuccess: () => state.devCompleted = false,
        });
    }
}

export default new Development();
