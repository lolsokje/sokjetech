export const percentage = (value: number, divisor: number): number => {
    return (value / divisor) * 100;
};

export const random = (min: number, max: number): number => {
    return Math.floor(Math.random() * (max - min + 1) + min);
};
