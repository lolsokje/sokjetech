import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faBars,
    faCaretDown,
    faCaretRight,
    faCheck,
    faGlobe,
    faHome,
    faPlus,
    faRoad,
    faSignInAlt,
    faSignOutAlt,
    faThList,
    faTimes,
} from '@fortawesome/free-solid-svg-icons';

library.add(faBars);
library.add(faTimes);
library.add(faCaretRight);
library.add(faCaretDown);
library.add(faRoad);
library.add(faThList);
library.add(faHome);
library.add(faPlus);
library.add(faSignInAlt);
library.add(faSignOutAlt);
library.add(faGlobe);
library.add(faCheck);

export default FontAwesomeIcon;
