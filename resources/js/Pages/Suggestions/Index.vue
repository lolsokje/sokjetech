<template>
    <h3>Suggestions</h3>

    <InertiaLink :href="route('suggestions.create')" class="btn btn-primary my-3">Submit a suggestion</InertiaLink>

    <div class="row mb-3">
        <div class="col-6">
            <select v-model="params.only" class="form-control">
                <option value="">Show only ...</option>
                <option value="open">Open suggestions</option>
                <option value="closed">Closed suggestions</option>
            </select>
        </div>
    </div>
    <template v-if="suggestions">
        <table class="table">
            <thead>
            <tr>
                <th>Summary</th>
                <th role="button" @click.prevent="sort(params, 'type')">
                    Type
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="type"/>
                </th>
                <th>Suggested by</th>
                <th class="text-center" role="button" @click.prevent="sort(params, 'status')">
                    Status
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="status"/>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="suggestion in suggestions" :key="suggestion.id">
                <td class="padded-left">{{ suggestion.summary }}</td>
                <td class="padded-left">{{ suggestion.type }}</td>
                <td class="padded-left">{{ suggestion.user }}</td>
                <td class="biggest-centered" :class="getStatusClass(suggestion.status)">{{ suggestion.status }}</td>
                <td class="small-centered">
                    <InertiaLink :href="route('suggestions.edit', suggestion)">view</InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No suggestions found</p>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { filter, sort } from '@/Composables/useTableFiltering';
import Pagination from '@/Shared/Pagination.vue';
import OrderIcon from '@/Shared/OrderIcon.vue';
import { getStatusClass } from '@/Composables/useStatusClasses';

const props = defineProps({
    suggestions: Array,
    links: Array,
    filters: Object,
});

const params = reactive({
    only: props.filters.only ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    page: props.filters.page ?? '',
});

watch(params, () => {
    filter(params, route('suggestions.index'));
});
</script>
