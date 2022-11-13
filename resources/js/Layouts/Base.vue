<template>
    <Nav/>

    <div id="content" class="px-5 mb-3">
        <Toast/>

        <main>
            <slot/>
        </main>
    </div>

    <div class="d-flex fixed-bottom">
        <div class="dropup ms-auto">
            <button type="button"
                    class="dropdown-toggle btn btn-primary btn-no-radius"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
            >

            </button>
            <ul class="dropdown-menu">
                <li class="dropdown-item"
                    role="button"
                    v-for="theme in themes"
                    :key="theme"
                    :style="getStyle(theme)"
                    @click.prevent="setTheme(theme)"
                >
                    {{ theme.label }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import Toast from '../Shared/Toast.vue';
import Nav from '@/Shared/Nav.vue';

const themes = [
    { name: 'dark', label: 'Dark', background: '#15151E', color: '#F8B739' },
    { name: 'light', label: 'Light', background: '#F4F4F2', color: '#005B96' },
    { name: 'f1-dark', label: 'F1 Dark', background: '#15151E', color: '#E10600' },
    { name: 'f1-light', label: 'F1 Light', background: '#FFFFFF', color: '#E10600' },
];

const getStyle = (theme) => {
    return `background-color: ${theme.background}; color: ${theme.color}`;
};

const setTheme = (theme) => {
    localStorage.setItem('theme', theme.name);
    document.querySelector('html').dataset.theme = theme.name;
};
</script>

<script>
export default { name: 'Base' };
</script>

<style scoped>
.btn-no-radius {
    border-radius: 0 !important;
}
</style>
