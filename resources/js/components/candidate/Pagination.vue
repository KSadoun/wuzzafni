<script setup lang="ts">
import { computed } from 'vue';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

const props = defineProps<{
    pagination: {
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        from: number;
        to: number;
    } | null;
}>();

const emit = defineEmits<{
    (e: 'page-changed', page: number): void;
}>();

const currentPage = computed(() => props.pagination?.current_page ?? 1);
const lastPage = computed(() => props.pagination?.last_page ?? 1);
const total = computed(() => props.pagination?.total ?? 0);
const from = computed(() => props.pagination?.from ?? 0);
const to = computed(() => props.pagination?.to ?? 0);

const showPagination = computed(() => lastPage.value > 1);

const pages = computed(() => {
    const current = currentPage.value;
    const last = lastPage.value;
    const pages: (number | '...')[] = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
        return pages;
    }

    // Always include first page
    pages.push(1);

    if (current > 4) pages.push('...');

    const start = Math.max(2, current - 2);
    const end = Math.min(last - 1, current + 2);

    for (let i = start; i <= end; i++) pages.push(i);

    if (current < last - 3) pages.push('...');

    // Always include last page
    pages.push(last);

    return pages;
});

function goToPage(page: number | '...') {
    if (typeof page !== 'number') return;
    if (page < 1 || page > lastPage.value || page === currentPage.value) return;
    emit('page-changed', page);
}
</script>

<template>
    <div v-if="pagination" class="flex flex-col items-center gap-4 mt-8">
        <!-- Results info -->
        <p class="text-sm text-gray-500">
            Showing <span class="font-semibold text-gray-800">{{ from }}</span>–<span class="font-semibold text-gray-800">{{ to }}</span>
            of <span class="font-semibold text-gray-800">{{ total }}</span> results
        </p>

        <!-- Pagination controls -->
        <div v-if="showPagination" class="flex items-center gap-1">
            <!-- First page -->
            <button
                @click="goToPage(1)"
                :disabled="currentPage === 1"
                title="First page"
                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            >
                <ChevronsLeft class="w-4 h-4" />
            </button>

            <!-- Previous page -->
            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                title="Previous page"
                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            >
                <ChevronLeft class="w-4 h-4" />
            </button>

            <!-- Page numbers -->
            <template v-for="(page, idx) in pages" :key="idx">
                <button
                    v-if="page !== '...'"
                    @click="goToPage(page)"
                    :class="[
                        'w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium border transition-all',
                        page === currentPage
                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-200'
                            : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 hover:border-gray-300'
                    ]"
                >
                    {{ page }}
                </button>
                <span v-else class="w-9 h-9 flex items-center justify-center text-gray-400 text-sm select-none">
                    &hellip;
                </span>
            </template>

            <!-- Next page -->
            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                title="Next page"
                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            >
                <ChevronRight class="w-4 h-4" />
            </button>

            <!-- Last page -->
            <button
                @click="goToPage(lastPage)"
                :disabled="currentPage === lastPage"
                title="Last page"
                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            >
                <ChevronsRight class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
