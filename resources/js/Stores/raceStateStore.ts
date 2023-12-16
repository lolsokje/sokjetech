import { reactive } from 'vue';
import { Race } from '@/Interfaces/Race';
import LapDetails from '@/Interfaces/Race/LapDetails';
import FastestLapDetails from '@/Interfaces/Race/FastestLapDetails';
import { RaceDriver } from '@/Interfaces/RaceWeekend/RaceWeekendDriver';

interface RaceStateStore {
    race: Race | null,
    currentLap: number,
    completed: boolean,
    saving: boolean,
    showError: boolean,
    lapDetails: LapDetails,
    fastestLapDetails: FastestLapDetails,
    setFastestLap: { (laptime: number, lap: number, driver: RaceDriver | null): void },
    isFastestLap: { (laptime: number): boolean }
}

export const raceStateStore: RaceStateStore = reactive({
    race: null,
    currentLap: 0,
    completed: false,
    saving: false,
    showError: false,
    lapDetails: {
        duration: 0,
        baseLaptime: 0,
        scale: 0,
        margin: 0,
        firstLapMargin: 0,
        perPoint: 0,
    },
    fastestLapDetails: {
        laptime: 0,
        lap: 0,
        driver: null,
    },

    setFastestLap (laptime: number, lap: number, driver: RaceDriver | null): void {
        this.fastestLapDetails.laptime = laptime;
        this.fastestLapDetails.lap = lap;
        this.fastestLapDetails.driver = driver;
    },

    isFastestLap (laptime: number): boolean {
        return laptime < this.fastestLapDetails.laptime && this.currentLap > 1;
    },
});
