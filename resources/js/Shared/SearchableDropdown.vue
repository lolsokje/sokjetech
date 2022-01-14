<template>
	<label class="form-label">{{ label }}</label>
	<div v-if="items" class="dropdown mb-3">
		<input v-model="searchFilter" class="dropdown-input form-control" @blur="exit" @focus="showItems">

		<div v-show="itemsShown" class="dropdown-content">
			<div v-for="(item, index) in filteredItems" :key="index" class="dropdown-item"
				 @mousedown="selectItem(item)">
				{{ item[textKey] }}
			</div>
		</div>
	</div>
</template>

<script setup>

import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
	valueKey: {
		type: String,
		required: false,
		default: 'id',
	},
	textKey: {
		type: String,
		required: false,
		default: 'name',
	},
	items: {
		type: Array,
		required: true,
	},
	selectedItem: {
		type: Object,
		required: false,
		default: {},
	},
	label: {
		type: String,
		required: false,
		default: 'Select an option',
	},
});

const selected = ref(props.selectedItem);
const itemsShown = ref(false);
const searchFilter = ref('');
const emit = defineEmits(['selected']);

const filteredItems = computed(() => {
	const searchKey = props.textKey;
	const search = searchFilter.value.toLowerCase();

	return props.items.filter((item) => item[searchKey].toLowerCase().includes(search));
});

function selectItem (option) {
	selected.value = option;
	itemsShown.value = false;
	searchFilter.value = selected.value[props.textKey];
	emit('selected', selected.value);
}

function showItems () {
	searchFilter.value = '';
	itemsShown.value = true;
}

function exit () {
	if (Object.keys(selected.value).length === 0) {
		itemsShown.value = false;
		searchFilter.value = '';
		return;
	}

	if (!selected.value[props.valueKey]) {
		selected.value = {};
		searchFilter.value = '';
	} else {
		searchFilter.value = selected.value[props.textKey];
	}
	emit('selected', selected.value);
	itemsShown.value = false;
}

onMounted(() => {
	if (Object.keys(selected.value).length !== 0) {
		searchFilter.value = selected.value[props.textKey];
	}
});

watch(() => props.selectedItem, (newValue) => {
	if (Object.keys(newValue).length === 0) {
		searchFilter.value = '';
		selected.value = {};
	}
});
</script>

<script>
export default { name: 'SearchableDropdown' };
</script>

<style lang="scss" scoped>
.dropdown {
	.dropdown-input {
		border: 1px solid #e7ecf5;
		color: #333;
		display: block;
		width: 50%;

		&:hover {
			background: #f8f8fa;
		}
	}

	.dropdown-content {
		position: absolute;
		background-color: #fff;
		width: 50%;
		max-height: 350px;
		overflow: auto;
		z-index: 1;

		.dropdown-item {
			color: black;
			line-height: 1em;
			padding: 8px;
			text-decoration: none;
			display: block;
			cursor: pointer;

			&:hover {
				background-color: #e7ecf5;
			}
		}
	}

	.dropdown:hover .dropdown-content {
		display: block;
	}
}
</style>
