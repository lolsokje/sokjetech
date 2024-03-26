<template>
    <div class="mt-4">
        <label class="form-label">Preview</label>
        <table class="table">
            <thead>
            <tr>
                <th class="colour-accent"></th>
                <th>Full name</th>
                <th>Short name</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <BackgroundColourCell :backgroundColour="team.accent_colour"/>
                <td class="padded-left">{{ team.full_name }}</td>
                <td class="padded-left">{{ team.short_name }}</td>
                <td class="small-centered">
                    <CountryFlag :country="team.country"/>
                </td>
                <td class="small-centered" :style="style">{{ number }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import BackgroundColourCell from '@/Components/BackgroundColourCell.vue';
import { getRoll } from '@/Composables/useRandom';

const props = defineProps({
    team: Object,
});

const number = getRoll(0, 999);

const style = computed(() => {
    return {
        backgroundColor: props.team.primary_colour,
        color: props.team.secondary_colour,
    };
});
</script>

<script>
export default { name: 'TeamNamePreview' };
</script>
