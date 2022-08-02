export function assignFormatDetails (defaultFormatDetails, storedFormatDetails) {
    if (!storedFormatDetails) {
        return;
    }

    Object.keys(storedFormatDetails).forEach(key => {
        if (defaultFormatDetails.hasOwnProperty(key)) {
            defaultFormatDetails[key] = storedFormatDetails[key];
        }
    });
}

export function getQualifyingFormatComponentName (formatType) {
    return formatType.split('\\').at(-1)
        .split(/(?=[A-Z])/) // split at uppercase letters
        .join('_') // join with underscores
        .toLowerCase();
}
