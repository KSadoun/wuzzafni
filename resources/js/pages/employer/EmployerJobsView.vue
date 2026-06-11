<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { 
    Loader2, 
    Briefcase, 
    Eye, 
    FileText, 
    Plus, 
    Calendar, 
    MapPin, 
    Clock, 
    Trash2, 
    Edit, 
    ExternalLink 
} from 'lucide-vue-next';
import { useEmployerJobsStore } from '@/stores/employerJobsStore';
import Pagination from '@/components/candidate/Pagination.vue';

const employerJobsStore = useEmployerJobsStore();

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('page');
    const initialPage = pageParam ? parseInt(pageParam, 10) : 1;
    employerJobsStore.fetchJobs(initialPage);
});

function handlePageChange(page: number) {
    employerJobsStore.fetchJobs(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function handleDelete(jobId: number) {
    if (confirm('Are you sure you want to delete this job posting? This action cannot be undone.')) {
        await employerJobsStore.deleteJob(jobId);
    }
}

// Helper functions for statuses
function getStatusBadgeClass(status: string) {
    switch (status) {
        case 'active':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20';
        case 'draft':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 ring-1 ring-blue-600/20';
        case 'closed':
            return 'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400 ring-1 ring-gray-600/20';
        default:
            return 'bg-gray-50 text-gray-600 ring-1 ring-gray-600/20';
    }
}

function getWorkTypeLabel(workType: string) {
    if (workType === 'onsite') {
return 'On-site';
}

    return workType.charAt(0).toUpperCase() + workType.slice(1);
}
</script>

<template>
    <Head title="My Job Posts" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Manage Job Openings</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Post new job opportunities, track applicants, and manage your active listings.
                </p>
            </div>
            <div>
                <Link
                    href="/employer/jobs/create"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm hover:shadow dark:bg-indigo-500 dark:hover:bg-indigo-600"
                >
                    <Plus class="w-5 h-5" />
                    <span>Post a New Job</span>
                </Link>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <Briefcase class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">Total Listings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ employerJobsStore.pagination?.total ?? employerJobsStore.jobs.length }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <Clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">Active Openings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ employerJobsStore.jobs.filter(j => j.status === 'active').length }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <Eye class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ employerJobsStore.jobs.reduce((sum, job) => sum + (job.views_count || 0), 0) }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <FileText class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">Total Applicants</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ employerJobsStore.jobs.reduce((sum, job) => sum + (job.applications_count || 0), 0) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Initial Full-page Loader -->
        <div v-if="employerJobsStore.isLoading && employerJobsStore.jobs.length === 0"
            class="flex flex-col justify-center items-center py-24 gap-4">
            <Loader2 class="w-10 h-10 text-indigo-500 animate-spin" />
            <p class="text-sm text-gray-500 dark:text-neutral-400">Loading your job posts…</p>
        </div>

        <!-- Error State -->
        <div v-else-if="employerJobsStore.error"
            class="bg-red-50 border border-red-200 dark:bg-red-950/20 dark:border-red-900/50 text-red-700 dark:text-red-400 p-6 rounded-2xl text-center shadow-sm">
            <h3 class="font-semibold text-lg mb-1">Failed to load listings</h3>
            <p class="text-sm opacity-90">{{ employerJobsStore.error }}</p>
            <button
                @click="employerJobsStore.fetchJobs()"
                class="mt-4 px-5 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition font-medium text-sm shadow-sm"
            >
                Retry
            </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!employerJobsStore.isLoading && employerJobsStore.jobs.length === 0"
            class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-3xl p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-950 text-indigo-300 dark:text-indigo-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <Briefcase class="w-10 h-10 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No jobs posted yet</h3>
            <p class="text-gray-500 dark:text-neutral-400 mb-8 max-w-md mx-auto">
                Create your first job posting today and start attracting top talent from our job board pool.
            </p>
            <Link
                href="/employer/jobs/create"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm"
            >
                <Plus class="w-5 h-5" />
                <span>Post Your First Job</span>
            </Link>
        </div>

        <!-- Job Postings List -->
        <div v-else>
            <!-- Loading overlay while changing pages -->
            <div class="relative">
                <Transition name="fade">
                    <div v-if="employerJobsStore.isLoading"
                        class="absolute inset-0 z-10 bg-white/70 dark:bg-neutral-950/70 backdrop-blur-xs flex items-center justify-center rounded-2xl">
                        <Loader2 class="w-8 h-8 text-indigo-500 animate-spin" />
                    </div>
                </Transition>

                <div class="space-y-6" :class="{ 'opacity-50 pointer-events-none': employerJobsStore.isLoading }">
                    <div 
                        v-for="job in employerJobsStore.jobs" 
                        :key="job.id"
                        class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-xs hover:shadow-md transition duration-200"
                    >
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                            <!-- Job Info and Pills -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition truncate">
                                        {{ job.title }}
                                    </h2>
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider ring-1"
                                        :class="getStatusBadgeClass(job.status)"
                                    >
                                        {{ job.status }}
                                    </span>
                                </div>

                                <!-- Meta Info Row -->
                                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 mt-3 text-sm text-gray-500 dark:text-neutral-400">
                                    <div class="flex items-center gap-1.5" v-if="job.location">
                                        <MapPin class="w-4 h-4 text-gray-400 dark:text-neutral-500" />
                                        <span>{{ job.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Briefcase class="w-4 h-4 text-gray-400 dark:text-neutral-500" />
                                        <span>{{ getWorkTypeLabel(job.work_type) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" v-if="job.experience_level">
                                        <Clock class="w-4 h-4 text-gray-400 dark:text-neutral-500" />
                                        <span class="capitalize">{{ job.experience_level }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" v-if="job.application_deadline">
                                        <Calendar class="w-4 h-4 text-gray-400 dark:text-neutral-500" />
                                        <span>Deadline: {{ job.application_deadline }}</span>
                                    </div>
                                </div>

                                <!-- Categories and Technologies -->
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <!-- Categories -->
                                    <span 
                                        v-for="cat in job.categories" 
                                        :key="cat.id"
                                        class="px-2.5 py-1 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 rounded-lg text-xs font-medium"
                                    >
                                        {{ cat.name }}
                                    </span>
                                    <!-- Technologies -->
                                    <span 
                                        v-for="tech in job.technologies" 
                                        :key="tech.id"
                                        class="px-2.5 py-1 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-medium ring-1 ring-indigo-500/10"
                                    >
                                        {{ tech.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Performance Stats -->
                            <div class="flex items-center gap-6 lg:border-l lg:border-gray-100 dark:lg:border-neutral-800 lg:pl-6">
                                <div class="text-center min-w-[70px]">
                                    <span class="block text-2xl font-extrabold text-gray-900 dark:text-white">
                                        {{ job.views_count || 0 }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">
                                        Views
                                    </span>
                                </div>
                                <div class="text-center min-w-[70px]">
                                    <span class="block text-2xl font-extrabold text-gray-950 dark:text-white">
                                        {{ job.applications_count || 0 }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">
                                        Applicants
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-row lg:flex-col items-center justify-end gap-3 w-full lg:w-auto">
                                <Link
                                    :href="`/employer/jobs/${job.id}/edit`"
                                    class="flex-1 lg:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-neutral-200 bg-gray-50 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl hover:bg-gray-100 dark:hover:bg-neutral-700 transition"
                                >
                                    <Edit class="w-4 h-4" />
                                    <span>Edit</span>
                                </Link>
                                <Link
                                    :href="`/jobs/${job.id}`"
                                    target="_blank"
                                    class="flex-1 lg:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-950 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-950/40 transition"
                                >
                                    <ExternalLink class="w-4 h-4" />
                                    <span>View</span>
                                </Link>
                                <button
                                    @click="handleDelete(job.id)"
                                    class="inline-flex items-center justify-center p-2 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 hover:bg-red-100 dark:hover:bg-red-950/40 rounded-xl transition"
                                    title="Delete job posting"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                :pagination="employerJobsStore.pagination"
                @page-changed="handlePageChange"
            />
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
