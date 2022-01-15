export function performDev (item) {
    const min = item.min;
    const max = item.max;

    const dev = Math.round(Math.random() * (max - min) + min);
    item.dev = dev;
    item.new = item.rating + dev;
}
