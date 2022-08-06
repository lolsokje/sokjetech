import { reactive } from 'vue';
import { TabLink } from '@/Utilities/TabLink';

export let raceWeekendStore = reactive({
    race: null,
    qualifying_completed: false,
    completed: false,
    links: [],

    initialiseLinks (race) {
        this.race = race;
        this.qualifying_completed = race.qualifying_completed;
        this.completed = race.completed;
        this.refreshLinks();
    },

    refreshLinks () {
        this.links = [
            {
                name: 'intro', link: new TabLink('weekend.intro', 'Intro', [ this.race ]),
            },
            {
                name: 'qualifying', link: new TabLink('weekend.qualifying', 'Qualifying', [ this.race ]),
            },
            {
                name: 'grid', link: new TabLink('weekend.grid', 'Grid', [ this.race ], this.qualifying_completed),
            },
            {
                name: 'race', link: new TabLink('weekend.race', 'Race', [ this.race ], this.qualifying_completed),
            },
            {
                name: 'results', link: new TabLink('weekend.results', 'Results', [ this.race ], this.completed),
            },
        ];
    },

    completeQualifying () {
        this.qualifying_completed = true;
        this.refreshLinks();
    },

    completeRace () {
        this.completed = true;
        this.refreshLinks();
    },

    getLinks () {
        return this.links.map(link => link.link);
    },
});
