import { reactive } from 'vue';

export let raceStore = reactive({
    saving: false,
    error: false,

    raceId: 0,
    completed: false,

    drivers: [],

    stintCount: 0,
    currentStint: 0,
    stintDetails: {},

    reliabilityMinRng: 0,
    reliabilityMaxRng: 0,
    reliabilityReasons: {},

    fastestLapIsAwarded: false,
    fastestLapIsSeparateStint: false,
    fastestLapRunCompleted: false,
    fastestLapMinRng: 0,
    fastestLapMaxRng: 0,

    setRaceId (id) {
        this.raceId = id;
    },

    setRaceCompleted (completed) {
        this.completed = completed;
    },

    setDrivers (drivers) {
        this.drivers = drivers;
    },

    setStintCount (count) {
        this.stintCount = count;
    },

    setCurrentStint (stint) {
        this.currentStint = stint;
    },

    incrementCurrentStint () {
        this.setCurrentStint(this.currentStint + 1);
    },

    allStintsCompleted () {
        return this.currentStint === this.stintCount;
    },

    setStintDetails (details) {
        this.stintDetails = details;
    },

    getCurrentStintDetails (index) {
        return this.stintDetails[index];
    },

    setReliabilityRng (minRng, maxRng) {
        this.reliabilityMinRng = minRng;
        this.reliabilityMaxRng = maxRng;
    },

    setReliabilityReasons (reasons) {
        this.reliabilityReasons = reasons;
    },

    getReliabilityReasonByType (type) {
        return this.reliabilityReasons[type];
    },

    getRandomReliabilityReason (type) {
        const reasons = this.getReliabilityReasonByType(type);
        return reasons[Math.floor(Math.random() * reasons.length)];
    },

    setFastestLapDetails (fastestLapDetails) {
        this.fastestLapIsAwarded = fastestLapDetails.awarded;
        this.fastestLapIsSeparateStint = fastestLapDetails.type === 'separate_stint';
        this.fastestLapMinRng = fastestLapDetails.min_rng;
        this.fastestLapMaxRng = fastestLapDetails.max_rng;
    },

    shouldRollFastestLap () {
        return this.fastestLapIsAwarded;
    },

    setFastestLapRunCompleted (completed) {
        this.fastestLapRunCompleted = completed;
    },

    completeFastestLapRun () {
        this.fastestLapRunCompleted = true;
    },

    canPerformNextStint () {
        return !this.allStintsCompleted() && !this.error;
    },

    canPerformFastestLapRoll () {
        return this.allStintsCompleted() && this.shouldRollFastestLap() && !this.fastestLapRunCompleted;
    },

    canCompleteRace () {
        if (!this.allStintsCompleted()) {
            return false;
        }

        if (this.completed) {
            return false;
        }

        if (!this.fastestLapIsAwarded) {
            return true;
        }

        return this.fastestLapRunCompleted;
    },

    setShowError (value) {
        this.error = value;
    },
});
