import { reactive } from 'vue';

export let threeSessionEliminationStore = reactive({
    drivers: [],
    currentSessionIndex: 0,
    completedRunsPerSession: {
        0: 0,
        1: 0,
        2: 0,
    },
    minRng: 0,
    maxRng: 0,

    setDrivers (value) {
        this.drivers = value;
    },

    getDrivers () {
        return this.drivers;
    },

    /**
     * returns the current session zero-indexed
     * @returns {number}
     */
    getCurrentSessionIndex () {
        return this.currentSessionIndex;
    },

    /**
     * returns the current session one-index
     * @returns {number}
     */
    getCurrentSessionNumber () {
        return this.getCurrentSessionIndex() + 1;
    },

    setCurrentSession (value) {
        this.currentSessionIndex = value;
    },

    incrementCurrentSession () {
        this.currentSessionIndex++;
    },

    decrementCurrentSession () {
        this.currentSessionIndex--;
    },

    getCompletedRunsPerSession () {
        return this.completedRunsPerSession;
    },

    setCompletedRunsPerSession (runsPerSession) {
        this.completedRunsPerSession = runsPerSession;
    },

    getCurrentSessionRunCount () {
        return this.completedRunsPerSession[this.getCurrentSessionIndex()];
    },

    incrementCompletedRunsForCurrentSession () {
        this.completedRunsPerSession[this.getCurrentSessionIndex()]++;
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
});
