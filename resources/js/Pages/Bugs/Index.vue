<template>
    <h3>Bugs</h3>

    <InertiaLink :href="route('bugs.create')" class="btn btn-primary my-3">Report bug</InertiaLink>

    <template v-if="bugs.length">
        <div class="row mb-3">
            <div class="col-6">
                <select v-model="params.only" class="form-control">
                    <option value="">Show only ...</option>
                    <option value="open">Open bugs</option>
                    <option value="closed">Closed bugs</option>
                </select>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Summary</th>
                <th role="button" @click.prevent="sort(params, 'type')">
                    Type
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="type"/>
                </th>
                <th>Reported by</th>
                <th class="text-center" role="button" @click.prevent="sort(params, 'status')">
                    Status
                    <OrderIcon :current-field="params.field" :direction="params.direction" required-field="status"/>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="bug in bugs" :key="bug.id">
                <td class="padded-left">{{ bug.summary }}</td>
                <td class="padded-left">{{ bug.type }}</td>
                <td class="padded-left">{{ bug.user }}</td>
                <td class="biggest-centered" :class="getStatusClass(bug.status)">{{ bug.status }}</td>
                <td class="small-centered">
                    <InertiaLink :href="route('bugs.edit', bug)">view</InertiaLink>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :links="links"/>
    </template>
    <p v-else>No bugs reported yet</p>
</template>

<script setup>
import { defineProps, reactive, watch } from 'vue';
import Pagination from '@/Shared/Pagination';
import OrderIcon from '@/Shared/OrderIcon';
import { getStatusClass } from '@/Composables/useStatusClasses';
import { filter, sort } from '@/Composables/useTableFiltering';

const props = defineProps({
    bugs: Array,
    links: Array,
    filters: Object,
});

const params = reactive({
    type: props.filters.type ?? '',
    field: props.filters.field ?? '',
    direction: props.filters.direction ?? '',
    only: props.filters.only ?? '',
    page: props.filters.page ?? '',
});

watch(params, () => {
    filter(params, route('bugs.index'));
});
</script>
