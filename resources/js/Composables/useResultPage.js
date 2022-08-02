export function getResultClasses (result) {
    if (result === undefined) {
        return '';
    }

    const classes = [];

    if (result.dnf) {
        classes.push('bg-danger');
    } else if (result.position === 1) {
        classes.push('position-first');
    } else if (result.position === 2) {
        classes.push('position-second');
    } else if (result.position === 3) {
        classes.push('position-third');
    } else if (result.position >= 4 && result.position <= 10) {
        classes.push('position-points');
    } else {
        classes.push('position-other');
    }

    if (result.starting_position === 1) {
        classes.push('fst-italic');
    }

    if (result.fastest_lap) {
        classes.push('text-decoration-underline');
    }

    return classes.join(' ');
}

export function sortResults (entities) {
    entities.sort((entityOne, entityTwo) => entityTwo.points - entityOne.points);
}
