<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useJobsStore } from '@/stores/jobsStore';
import { onMounted } from 'vue';
import JobCard from '@/components/candidate/JobCard.vue';
import JobFilters from '@/components/candidate/JobFilters.vue';
import JobSearchBar from '@/components/candidate/JobSearchBar.vue';
import Pagination from '@/components/candidate/Pagination.vue';
import { Loader2 } from 'lucide-vue-next';

const jobsStore = useJobsStore();

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const initialFilters: Record<string, any> = {};

    if (urlParams.has('keyword')) initialFilters.keyword = urlParams.get('keyword');
    if (urlParams.has('location')) initialFilters.location = urlParams.get('location');
    if (urlParams.has('work_type')) initialFilters.work_type = urlParams.get('work_type');
    if (urlParams.has('salary_min')) initialFilters.salary_min = urlParams.get('salary_min');
    if (urlParams.has('salary_max')) initialFilters.salary_max = urlParams.get('salary_max');
    if (urlParams.has('date_posted')) initialFilters.date_posted = urlParams.get('date_posted');
    if (urlParams.has('page')) initialFilters.page = parseInt(urlParams.get('page') || '1', 10);

    Object.assign(jobsStore.filters, initialFilters);
    jobsStore.fetchJobs();
});

function handleSearch() {
    jobsStore.setFilters({ keyword: jobsStore.filters.keyword });
}

function handleFilterUpdate(newFilters: Record<string, any>) {
    jobsStore.setFilters(newFilters);
}

function handlePageChange(page: number) {
    jobsStore.setPage(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
    <Head title="Find Jobs" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Bar -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900 text-center mb-6">Find your dream job</h1>
            <JobSearchBar v-model="jobsStore.filters.keyword" @search="handleSearch" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <JobFilters :filters="jobsStore.filters" @update="handleFilterUpdate" />
            </div>

            <!-- Job Listings -->
            <div class="lg:col-span-3">
                <!-- Initial full-page loading (no data yet) -->
                <div v-if="jobsStore.isLoading && jobsStore.jobs.length === 0"
                    class="flex justify-center items-center py-24">
                    <Loader2 class="w-10 h-10 text-indigo-500 animate-spin" />
                </div>

                <!-- Error state -->
                <div v-else-if="jobsStore.error"
                    class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-xl text-center">
                    <p class="font-medium">{{ jobsStore.error }}</p>
                    <button @click="jobsStore.fetchJobs()"
                        class="mt-3 text-sm underline font-medium hover:text-red-900 transition">
                        Try again
                    </button>
                </div>

                <!-- Empty state (after loading, no results) -->
                <div v-else-if="!jobsStore.isLoading && jobsStore.jobs.length === 0"
                    class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">🔍</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No jobs found</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">
                        Try adjusting your filters or search terms to find what you're looking for.
                    </p>
                    <button @click="jobsStore.setFilters({})"
                        class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                        Clear all filters
                    </button>
                </div>

                <!-- Jobs list with inline loading overlay on page change -->
                <div v-else>
                    <!-- Results count -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <span class="text-indigo-600 font-bold">{{ jobsStore.pagination?.total ?? jobsStore.jobs.length }}</span>
                            jobs found
                        </h2>
                        <span v-if="jobsStore.pagination && jobsStore.pagination.last_page > 1"
                            class="text-sm text-gray-500">
                            Page {{ jobsStore.pagination.current_page }} of {{ jobsStore.pagination.last_page }}
                        </span>
                    </div>

                    <!-- List with loading overlay -->
                    <div class="relative">
                        <Transition name="fade">
                            <div v-if="jobsStore.isLoading"
                                class="absolute inset-0 z-10 bg-white/70 backdrop-blur-sm flex items-center justify-center rounded-xl">
                                <Loader2 class="w-8 h-8 text-indigo-500 animate-spin" />
                            </div>
                        </Transition>

                        <div class="space-y-4" :class="{ 'opacity-50 pointer-events-none': jobsStore.isLoading }">
                            <Link
                                v-for="job in jobsStore.jobs"
                                :key="job.id"
                                :href="`/jobs/${job.id}`"
                                class="block"
                            >
                                <JobCard :job="job" />
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <Pagination
                        :pagination="jobsStore.pagination"
                        @page-changed="handlePageChange"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
