<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Loader2, LayoutList, BriefcaseBusiness } from 'lucide-vue-next';
import ApplicationCard from '@/components/candidate/ApplicationCard.vue';
import Pagination from '@/components/candidate/Pagination.vue';
import { useApplicationsStore } from '@/stores/applicationsStore';

const applicationsStore = useApplicationsStore();

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('page');
    const initialPage = pageParam ? parseInt(pageParam, 10) : 1;
    applicationsStore.fetchApplications(initialPage || 1);
});

function handleCancel(applicationId: number) {
    if (confirm('Are you sure you want to cancel this application?')) {
        applicationsStore.cancelApplication(applicationId);
    }
}

function handlePageChange(page: number) {
    applicationsStore.fetchApplications(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
    <Head title="My Applications" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                <BriefcaseBusiness class="w-5 h-5 text-indigo-600" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Applications</h1>
                <p v-if="applicationsStore.pagination" class="text-sm text-gray-500">
                    {{ applicationsStore.pagination.total }} application{{ applicationsStore.pagination.total !== 1 ? 's' : '' }} total
                </p>
            </div>
        </div>

        <!-- Initial loading (no data yet) -->
        <div v-if="applicationsStore.isLoading && applicationsStore.applications.length === 0"
            class="flex flex-col items-center justify-center py-24 gap-4">
            <Loader2 class="w-10 h-10 text-indigo-500 animate-spin" />
            <p class="text-gray-500 text-sm">Loading your applications…</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="!applicationsStore.isLoading && applicationsStore.applications.length === 0"
            class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-indigo-50 text-indigo-300 rounded-full flex items-center justify-center mx-auto mb-6">
                <LayoutList class="w-10 h-10" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No applications yet</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                You haven't applied to any jobs yet. Start browsing to find your next great opportunity.
            </p>
            <a href="/jobs"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                Browse Jobs
            </a>
        </div>

        <!-- Applications list -->
        <div v-else>
            <!-- Loading overlay while changing pages -->
            <div class="relative">
                <Transition name="fade">
                    <div v-if="applicationsStore.isLoading"
                        class="absolute inset-0 z-10 bg-white/70 backdrop-blur-sm flex items-center justify-center rounded-xl">
                        <Loader2 class="w-8 h-8 text-indigo-500 animate-spin" />
                    </div>
                </Transition>

                <div class="space-y-4" :class="{ 'opacity-50 pointer-events-none': applicationsStore.isLoading }">
                    <ApplicationCard
                        v-for="app in applicationsStore.applications"
                        :key="app.id"
                        :application="app"
                        @cancel="handleCancel"
                    />
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                :pagination="applicationsStore.pagination"
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
