<template>
    <nav class="navbar navbar-expand pt-3 pb-5">
        <div class="container">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <InertiaLink :href="route('index')" class="nav-link">Home</InertiaLink>
                </li>
                <li class="nav-item" v-if="user">
                    <InertiaLink :href="route('circuits.index')" class="nav-link">Circuits</InertiaLink>
                </li>
                <li class="nav-item">
                    <InertiaLink :href="route('universes.index')" class="nav-link">Universes</InertiaLink>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <template v-if="user">
                        <InertiaLink :href="route('auth.logout')" as="button" class="btn btn-link nav-link"
                                     method="POST"
                        >
                            Logout
                        </InertiaLink>
                    </template>
                    <template v-else>
                        <a :href="route('auth.redirect')" class="nav-link">Log in</a>
                    </template>
                </li>
            </ul>
        </div>
    </nav>
</template>
<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

const user = computed(() => usePage().props.value.auth.user);
</script>
