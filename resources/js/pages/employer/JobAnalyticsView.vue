<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { analyticsService } from '@/services/employerService';
import type { JobAnalytics } from '@/types/api';
import { Loader2, ArrowLeft, BarChart3, Users, Clock, CheckCircle, XCircle, Eye } from 'lucide-vue-next';

const props = defineProps<{ jobId: string }>();

const analytics = ref<JobAnalytics | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
    try {
        analytics.value = await analyticsService.getJobAnalytics(props.jobId);
    } catch (err: any) {
        error.value = err.message || 'Failed to load analytics.';
    } finally {
        isLoading.value = false;
    }
});

const statCards = [
    { key: 'total_applications', label: 'Total Applications', icon: Users, color: 'bg-blue-500' },
    { key: 'applications_per_day', label: 'Applications / Day', icon: Clock, color: 'bg-purple-500' },
    { key: 'pending', label: 'Pending', icon: Eye, color: 'bg-yellow-500' },
    { key: 'reviewed', label: 'Reviewed', icon: BarChart3, color: 'bg-indigo-500' },
    { key: 'accepted', label: 'Accepted', icon: CheckCircle, color: 'bg-green-500' },
    { key: 'rejected', label: 'Rejected', icon: XCircle, color: 'bg-red-500' },
] as const;
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <RouterLink :to="{ name: 'employer.jobs' }"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors font-medium mb-6">
            <ArrowLeft class="w-4 h-4" />
            Back to Jobs
        </RouterLink>

        <div v-if="isLoading" class="flex justify-center py-24">
            <Loader2 class="w-10 h-10 text-emerald-500 animate-spin" />
        </div>

        <div v-else-if="error" class="bg-red-50 text-red-600 p-8 rounded-xl text-center border border-red-100">
            {{ error }}
        </div>

        <template v-else-if="analytics">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <BarChart3 class="w-5 h-5 text-emerald-600" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Job Analytics</h1>
                    <p class="text-sm text-gray-500">{{ analytics.job.title }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="stat in statCards" :key="stat.key"
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div :class="['w-10 h-10 rounded-lg flex items-center justify-center text-white', stat.color]">
                            <component :is="stat.icon" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ stat.label }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ analytics[stat.key] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Status Breakdown</h2>
                <div class="space-y-3">
                    <div v-for="status in ['pending', 'reviewed', 'accepted', 'rejected']" :key="status">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="capitalize text-gray-600">{{ status }}</span>
                            <span class="font-medium text-gray-900">
                                {{ analytics[status as keyof JobAnalytics] }}
                                <span v-if="analytics.total_applications > 0" class="text-gray-400">
                                    ({{ Math.round((analytics[status as keyof JobAnalytics] as number / analytics.total_applications) * 100) }}%)
                                </span>
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all"
                                :class="{
                                    'bg-yellow-400': status === 'pending',
                                    'bg-indigo-400': status === 'reviewed',
                                    'bg-green-500': status === 'accepted',
                                    'bg-red-400': status === 'rejected',
                                }"
                                :style="{ width: analytics.total_applications > 0 ? `${(analytics[status as keyof JobAnalytics] as number / analytics.total_applications) * 100}%` : '0%' }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <RouterLink
                    :to="{ name: 'employer.job-applications', params: { jobId: props.jobId } }"
                    class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                    View all applications →
                </RouterLink>
            </div>
        </template>
    </div>
</template>
