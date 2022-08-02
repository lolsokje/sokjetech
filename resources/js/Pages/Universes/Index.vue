<template>
    <h1>Universes</h1>

    <BackLink :backTo="route('index')" label="index page"/>

    <InertiaLink :href="route('universes.create')" class="btn btn-primary my-3" v-if="user">Add universe</InertiaLink>

    <table v-if="universes.length" class="table table-bordered table-dark table-narrow">
        <thead>
        <tr>
            <th>Name</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="universe in universes" v-bind:key="universe.id">
            <td class="padded-left">{{ universe.name }}</td>
            <td class="small-centered">
                <InertiaLink v-if="universe.can.edit" :href="route('universes.edit', universe)">edit</InertiaLink>
            </td>
            <td class="small-centered">
                <InertiaLink :href="route('universes.show', universe)">view</InertiaLink>
            </td>
        </tr>
        </tbody>
    </table>
    <p v-else>No universes added yet</p>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import BackLink from '@/Shared/BackLink';

const props = defineProps({
    universes: {
        type: Array,
        required: true,
    },
});

const user = computed(() => usePage().props.value.auth.user);
</script>
