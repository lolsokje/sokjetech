<template>
    <nav class="navbar navbar-expand pt-3 pb-5">
        <div class="container">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <InertiaLink :href="route('index')" class="nav-link">Home</InertiaLink>
                </li>
                <li class="nav-item">
                    <InertiaLink :href="route('universes.index')" class="nav-link">Universes</InertiaLink>
                </li>
                <li class="nav-item">
                    <InertiaLink :href="route('tutorials')" class="nav-link">Tutorials</InertiaLink>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">
                        Content
                    </a>
                    <ul class="dropdown-menu" v-if="user">
                        <li class="nav-item">
                            <InertiaLink :href="route('database.index')" class="nav-link">
                                Database
                            </InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink :href="route('circuits.index')" class="nav-link">Circuits</InertiaLink>
                        </li>
                        <li class="nav-item">
                            <InertiaLink :href="route('climates.index')" class="nav-link">Climates</InertiaLink>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <template v-if="user">
                    <li class="nav-item">
                        <InertiaLink :href="route('bugs.index')" class="nav-link">Report bug</InertiaLink>
                    </li>
                    <li class="nav-item">
                        <InertiaLink :href="route('suggestions.index')" class="nav-link">Suggestions</InertiaLink>
                    </li>
                </template>
                <li class="nav-item">
                    <template v-if="user">
                        <InertiaLink :href="route('auth.logout')" as="button" class="btn btn-link"
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
import { usePage } from '@inertiajs/vue3';

const user = computed(() => usePage().props.auth.user);
</script>
