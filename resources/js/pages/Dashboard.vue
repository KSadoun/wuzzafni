<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import {
    Loader2,
    Briefcase,
    Clock,
    CheckCircle,
    XCircle,
    Eye,
    ArrowRight,
    LayoutGrid,
} from 'lucide-vue-next';
import { applicationService } from '@/services/applicationService';
import type { CandidateDashboard } from '@/types/api';
import { dashboard } from '@/routes';
import candidate from '@/routes/candidate';
import jobs from '@/routes/jobs';

const page = usePage();
const user = page.props.auth.user as { name?: string; first_name?: string };

const data = ref<CandidateDashboard | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);

const statCards = [
    { key: 'total', label: 'Total Applications', icon: Briefcase, color: 'bg-indigo-500' },
    { key: 'pending', label: 'Pending', icon: Clock, color: 'bg-yellow-500' },
    { key: 'reviewed', label: 'Reviewed', icon: Eye, color: 'bg-blue-500' },
    { key: 'accepted', label: 'Accepted', icon: CheckCircle, color: 'bg-green-500' },
    { key: 'rejected', label: 'Rejected', icon: XCircle, color: 'bg-red-500' },
] as const;

onMounted(async () => {
    try {
        data.value = await applicationService.getDashboard();
    } catch (err: any) {
        error.value = err.message || 'Failed to load dashboard.';
    } finally {
        isLoading.value = false;
    }
});

function getStatusColor(status: string) {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'accepted': return 'bg-green-100 text-green-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'reviewed': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                <LayoutGrid class="w-5 h-5 text-indigo-600" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Welcome back{{ user?.name ? `, ${user.name}` : user?.first_name ? `, ${user.first_name}` : '' }}
                </h1>
                <p class="text-sm text-gray-500">Track your job applications and find new opportunities</p>
            </div>
        </div>

        <div v-if="isLoading" class="flex justify-center py-24">
            <Loader2 class="w-10 h-10 text-indigo-500 animate-spin" />
        </div>

        <div v-else-if="error" class="bg-red-50 text-red-600 p-8 rounded-xl text-center border border-red-100">
            {{ error }}
        </div>

        <template v-else-if="data">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                <div v-for="stat in statCards" :key="stat.key"
                    class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center text-white mb-3', stat.color]">
                        <component :is="stat.icon" class="w-4 h-4" />
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ data.stats[stat.key] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ stat.label }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-8">
                <Link :href="jobs.index()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                    <Briefcase class="w-4 h-4" />
                    Browse Jobs
                </Link>
                <Link :href="candidate.applications()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 font-medium rounded-xl border border-indigo-200 hover:bg-indigo-50 transition">
                    View All Applications
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
                    <Link :href="candidate.applications()" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        See all
                    </Link>
                </div>

                <div v-if="data.recent_applications.length === 0" class="p-12 text-center">
                    <Briefcase class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-500 mb-4">You haven't applied to any jobs yet.</p>
                    <Link :href="jobs.index()" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                        Start browsing jobs →
                    </Link>
                </div>

                <div v-else class="divide-y divide-gray-100">
                    <div v-for="app in data.recent_applications" :key="app.id"
                        class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ app.job?.title || 'Unknown Job' }}</p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ (app.job as any)?.employerProfile?.company_name || 'Company' }}
                                · Applied {{ new Date(app.applied_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <span :class="['shrink-0 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize', getStatusColor(app.status)]">
                            {{ app.status }}
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
