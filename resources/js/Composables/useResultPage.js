export function getResultClasses (result, lastPointPayingPosition = 10) {
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
    } else if (result.position >= 4 && result.position <= lastPointPayingPosition) {
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
