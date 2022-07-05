export function assignFormatDetails(defaultFormatDetails, storedFormatDetails) {
    if (!storedFormatDetails) {
        return;
    }

    Object.keys(storedFormatDetails).forEach(key => {
        if (defaultFormatDetails.hasOwnProperty(key)) {
            defaultFormatDetails[key] = storedFormatDetails[key];
        }
    });
}
