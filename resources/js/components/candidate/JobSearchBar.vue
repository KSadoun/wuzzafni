<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'search'): void;
}>();

const localSearch = ref(props.modelValue);

function onSearch() {
    emit('update:modelValue', localSearch.value);
    emit('search');
}
</script>

<template>
    <div class="relative w-full max-w-2xl mx-auto">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
        </div>
        <input 
            type="text" 
            v-model="localSearch"
            @keyup.enter="onSearch"
            class="block w-full pl-11 pr-24 py-4 bg-white border border-gray-300 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition-all" 
            placeholder="Job title, keywords, or company..." 
        />
        <button 
            @click="onSearch"
            class="absolute inset-y-2 right-2 flex items-center px-6 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
            Search
        </button>
    </div>
</template>
