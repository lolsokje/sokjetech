import { reactive } from 'vue';
import { RaceEventFlag } from '@/Enums/RaceEventFlag';
import { raceStateStore } from '@/Stores/raceStateStore';
import { YellowFlagType } from '@/Enums/YellowFlagType';

export interface RaceFlagEvent {
    flag: RaceEventFlag,
    startLap: number,
    type?: YellowFlagType,
    expectedEndLap?: number,
    endLap?: number,
}

interface FlagEventStore {
    flags: RaceFlagEvent[],
    addFlagEvent: { (flag: RaceFlagEvent): void },
    lastFlagEvent: { (): RaceFlagEvent },
    lastYellowFlagEvent: { (): RaceFlagEvent | null },
    showYellowFlag: { (): boolean },
    showGreenFlag: { (): boolean },
}

const startFlag: RaceFlagEvent = {
    flag: RaceEventFlag.GREEN,
    startLap: 0,
};

export const flagEventStore: FlagEventStore = reactive({
    flags: [ startFlag ],

    addFlagEvent (flag: RaceFlagEvent): void {
        this.flags.push(flag);
    },

    lastFlagEvent (): RaceFlagEvent {
        return this.flags.at(-1);
    },

    lastYellowFlagEvent (): RaceFlagEvent | null {
        let event: RaceFlagEvent | null = null;

        this.flags.forEach(flagEvent => {
            if (flagEvent.flag === RaceEventFlag.YELLOW) {
                event = flagEvent;
            }
        });

        return event;
    },

    showYellowFlag (): boolean {
        return this.lastFlagEvent().flag === RaceEventFlag.YELLOW;
    },

    showGreenFlag (): boolean {
        return this.lastFlagEvent().flag === RaceEventFlag.GREEN && raceStateStore.currentLap === this.lastYellowFlagEvent()?.endLap;
    },
});
