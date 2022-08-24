export const getStatusClass = (status) => {
    status = status
        .toLowerCase()
        .replaceAll(' ', '-')
        .replaceAll("'", '');

    return `status-${status}`;
};
