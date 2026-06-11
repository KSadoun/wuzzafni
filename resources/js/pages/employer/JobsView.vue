<script setup lang="ts">
import { onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useEmployerStore } from '@/stores/employerStore';
import { Loader2, Briefcase, BarChart3, Users } from 'lucide-vue-next';

const employerStore = useEmployerStore();

onMounted(() => {
    employerStore.fetchJobs();
});

function handlePageChange(page: number) {
    employerStore.fetchJobs(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <Briefcase class="w-5 h-5 text-emerald-600" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Job Postings</h1>
                <p v-if="employerStore.pagination" class="text-sm text-gray-500">
                    {{ employerStore.pagination.total }} job{{ employerStore.pagination.total !== 1 ? 's' : '' }} total
                </p>
            </div>
        </div>

        <div v-if="employerStore.isLoading && employerStore.jobs.length === 0"
            class="flex flex-col items-center justify-center py-24 gap-4">
            <Loader2 class="w-10 h-10 text-emerald-500 animate-spin" />
            <p class="text-gray-500 text-sm">Loading your jobs…</p>
        </div>

        <div v-else-if="!employerStore.isLoading && employerStore.jobs.length === 0"
            class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
            <Briefcase class="w-16 h-16 mx-auto mb-4 text-gray-300" />
            <h3 class="text-xl font-bold text-gray-900 mb-2">No jobs posted yet</h3>
            <p class="text-gray-500">Create a job posting to start receiving applications.</p>
        </div>

        <div v-else class="space-y-4">
            <div v-for="job in employerStore.jobs" :key="job.id"
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ job.title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ job.location || 'Remote' }} · {{ job.work_type }} · {{ job.applications_count }} applications
                        </p>
                        <span :class="[
                            'inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',
                            job.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                        ]">
                            {{ job.status }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <RouterLink
                            :to="{ name: 'employer.job-applications', params: { jobId: job.id } }"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                            <Users class="w-4 h-4" />
                            Applications
                        </RouterLink>
                        <RouterLink
                            :to="{ name: 'employer.job-analytics', params: { jobId: job.id } }"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-emerald-700 text-sm font-medium rounded-lg border border-emerald-200 hover:bg-emerald-50 transition">
                            <BarChart3 class="w-4 h-4" />
                            Analytics
                        </RouterLink>
                    </div>
                </div>
            </div>

            <div v-if="employerStore.pagination && employerStore.pagination.last_page > 1"
                class="flex justify-center gap-2 pt-4">
                <button
                    v-for="page in employerStore.pagination.last_page"
                    :key="page"
                    :disabled="employerStore.isLoading"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        page === employerStore.pagination.current_page
                            ? 'bg-emerald-600 text-white'
                            : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'
                    ]"
                    @click="handlePageChange(page)">
                    {{ page }}
                </button>
            </div>
        </div>
    </div>
</template>
