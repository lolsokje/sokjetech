<template>
    <h1>SokjeTech</h1>

    <template v-if="races.length">
        <h3>Latest completed races</h3>

        <table class="table">
            <thead>
            <tr>
                <th>Race</th>
                <th>Universe</th>
                <th>Series</th>
                <th>Season</th>
                <th>User</th>
                <th>Completed</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="race in races" :key="race.id">
                <td class="padded-left">{{ race.name }}</td>
                <td class="padded-left">{{ race.universe }}</td>
                <td class="padded-left">{{ race.series }}</td>
                <td class="padded-left">{{ race.season }}</td>
                <td class="padded-left">{{ race.user }}</td>
                <td class="padded-left">
                    <span :title="race.completed_at">
                        {{ race.diff_for_humans }}
                    </span>
                </td>
                <td class="small-centered">
                    <InertiaLink :href="route('weekend.results', [race])">view</InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
    </template>
</template>

<script setup>
defineProps({
    races: Array,
});
</script>
