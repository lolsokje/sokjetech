import { reactive } from 'vue';

export let singleSessionStore = reactive({
    drivers: [],
    currentRun: 0,
    minRng: 0,
    maxRng: 0,

    setDrivers (value) {
        this.drivers = value;
    },

    getDrivers () {
        return this.drivers;
    },

    getMinRng () {
        return this.minRng;
    },

    setMinRng (value) {
        this.minRng = value;
    },

    getMaxRng () {
        return this.maxRng;
    },

    setMaxRng (value) {
        this.maxRng = value;
    },

    setCurrentRun (value) {
        this.currentRun = value;
    },

    incrementCurrentRun () {
        this.setCurrentRun(this.getCurrentRun() + 1);
    },

    getCurrentSessionIndex () {
        return 0;
    },

    getCurrentSessionRunCount () {
        return this.getCurrentRun();
    },

    getCurrentRun () {
        return this.currentRun;
    },

    resetQualifyingSessionStats () {
        this.setCurrentRun(0);
    },
});
