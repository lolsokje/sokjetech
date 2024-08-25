import { reactive } from 'vue';

const style: CSSStyleDeclaration = getComputedStyle(document.body);

export let themeStore = reactive({
    color: style.getPropertyValue('--color'),
    base300: style.getPropertyValue('--base-300'),
});
