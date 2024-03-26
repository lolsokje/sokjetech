import { reactive } from 'vue';
import { Race } from '@/Interfaces/Race';
import QualifyingResult from '@/Interfaces/Race/QualifyingResult';
import FormatDetails from '@/Interfaces/RaceWeekend/FormatDetails';

interface Store {
    race: Race,
    formatDetails: FormatDetails,
    results: QualifyingResult[],
    activeDrivers: QualifyingResult[],
    canRunQualifying: boolean,
    currentSession: number,
    totalSessions: number,
    currentRun: number,
    error: boolean,
    saving: boolean,
}

export let qualifyingStore: Store = reactive({
    race: {},
    formatDetails: {},
    results: [],
    activeDrivers: [],
    canRunQualifying: false,
    currentSession: 1,
    totalSessions: 1,
    currentRun: 1,
    error: false,
    saving: false,
});
