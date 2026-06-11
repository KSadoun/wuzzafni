<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    filters: Record<string, any>;
}>();

const emit = defineEmits<{
    (e: 'update', filters: Record<string, any>): void;
}>();

const localFilters = ref({ ...props.filters });

// Make sure localFilters array fields are initialized correctly
if (!Array.isArray(localFilters.value.category)) {
localFilters.value.category = [];
}

if (!Array.isArray(localFilters.value.technologies)) {
localFilters.value.technologies = [];
}

function applyFilters() {
    emit('update', localFilters.value);
}

function clearFilters() {
    localFilters.value = {
        keyword: localFilters.value.keyword || '',
        location: '',
        category: [],
        technologies: [],
        work_type: '',
        salary_min: '',
        salary_max: '',
        date_posted: '',
        page: 1
    };
    emit('update', localFilters.value);
}

watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters };

    if (!Array.isArray(localFilters.value.category)) {
localFilters.value.category = [];
}

    if (!Array.isArray(localFilters.value.technologies)) {
localFilters.value.technologies = [];
}
}, { deep: true });
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-gray-900">Filters</h3>
            <button @click="clearFilters" class="text-sm text-blue-600 hover:text-blue-800">Clear all</button>
        </div>

        <div class="space-y-6">
            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input 
                    type="text" 
                    v-model="localFilters.location"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="City, state, or country"
                />
            </div>

            <!-- Work Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Work Type</label>
                <select 
                    v-model="localFilters.work_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                >
                    <option value="">Any</option>
                    <option value="remote">Remote</option>
                    <option value="onsite">On-site</option>
                    <option value="hybrid">Hybrid</option>
                </select>
            </div>

            <!-- Minimum Salary -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Salary</label>
                    <input 
                        type="number" 
                        v-model="localFilters.salary_min"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g. 50000"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Salary</label>
                    <input 
                        type="number" 
                        v-model="localFilters.salary_max"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g. 100000"
                    />
                </div>
            </div>

            <!-- Date Posted -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Posted</label>
                <select 
                    v-model="localFilters.date_posted"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                >
                    <option value="">Any Time</option>
                    <option value="today">Past 24 hours</option>
                    <option value="week">Past Week</option>
                    <option value="month">Past Month</option>
                </select>
            </div>

            <button 
                @click="applyFilters"
                class="w-full py-2.5 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition-colors"
            >
                Apply Filters
            </button>
        </div>
    </div>
</template>

