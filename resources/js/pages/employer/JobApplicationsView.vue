<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useEmployerStore } from '@/stores/employerStore';
import { Loader2, ArrowLeft, CheckCircle, CreditCard, User } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{ jobId: string }>();

const employerStore = useEmployerStore();
const router = useRouter();
const acceptingId = ref<number | null>(null);

onMounted(() => {
    employerStore.fetchApplications(props.jobId);
});

function getStatusColor(status: string) {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'reviewed': return 'bg-blue-100 text-blue-800';
        case 'accepted': return 'bg-green-100 text-green-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

async function handleAccept(applicationId: number) {
    if (!confirm('Accept this candidate? You will be redirected to complete payment.')) {
        return;
    }

    acceptingId.value = applicationId;
    try {
        await employerStore.acceptApplication(applicationId);
        toast.success('Candidate accepted. Complete payment to finalize.');
        router.push({ name: 'employer.payment', params: { applicationId } });
    } catch (err: any) {
        toast.error(err.message || 'Failed to accept candidate.');
    } finally {
        acceptingId.value = null;
    }
}

function handlePageChange(page: number) {
    employerStore.fetchApplications(props.jobId, page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <RouterLink :to="{ name: 'employer.jobs' }"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors font-medium mb-6">
            <ArrowLeft class="w-4 h-4" />
            Back to Jobs
        </RouterLink>

        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <User class="w-5 h-5 text-emerald-600" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Applications</h1>
                <p v-if="employerStore.applicationsPagination" class="text-sm text-gray-500">
                    {{ employerStore.applicationsPagination.total }} applicant{{ employerStore.applicationsPagination.total !== 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <div v-if="employerStore.isLoading && employerStore.applications.length === 0"
            class="flex justify-center py-24">
            <Loader2 class="w-10 h-10 text-emerald-500 animate-spin" />
        </div>

        <div v-else-if="employerStore.error"
            class="bg-red-50 text-red-600 p-8 rounded-xl text-center border border-red-100">
            {{ employerStore.error }}
        </div>

        <div v-else-if="!employerStore.isLoading && employerStore.applications.length === 0"
            class="bg-white border border-gray-200 rounded-2xl p-16 text-center">
            <p class="text-gray-500">No applications for this job yet.</p>
        </div>

        <div v-else class="space-y-4">
            <div v-for="app in employerStore.applications" :key="app.id"
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ app.candidate.name || 'Candidate' }}</h3>
                            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium capitalize', getStatusColor(app.status)]">
                                {{ app.status }}
                            </span>
                        </div>
                        <p v-if="app.candidate.current_position" class="text-sm text-gray-600">{{ app.candidate.current_position }}</p>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                            <span v-if="app.candidate.email">{{ app.candidate.email }}</span>
                            <span v-if="app.candidate.phone">{{ app.candidate.phone }}</span>
                            <span>Applied {{ new Date(app.applied_at).toLocaleDateString() }}</span>
                        </div>
                        <p v-if="app.cover_letter" class="mt-3 text-sm text-gray-600 line-clamp-2">{{ app.cover_letter }}</p>
                        <a v-if="app.resume_url" :href="app.resume_url" target="_blank"
                            class="inline-block mt-3 text-sm text-emerald-600 hover:underline">
                            View Resume
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            v-if="['pending', 'reviewed'].includes(app.status)"
                            :disabled="acceptingId === app.id"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-50 transition"
                            @click="handleAccept(app.id)">
                            <Loader2 v-if="acceptingId === app.id" class="w-4 h-4 animate-spin" />
                            <CheckCircle v-else class="w-4 h-4" />
                            Accept
                        </button>
                        <RouterLink
                            v-if="app.status === 'accepted'"
                            :to="{ name: 'employer.payment', params: { applicationId: app.id } }"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition"
                            :class="app.payment?.payment_status === 'paid'
                                ? 'bg-green-100 text-green-800 cursor-default pointer-events-none'
                                : 'bg-amber-500 text-white hover:bg-amber-600'">
                            <CreditCard class="w-4 h-4" />
                            {{ app.payment?.payment_status === 'paid' ? 'Paid' : 'Pay Now' }}
                        </RouterLink>
                    </div>
                </div>
            </div>

            <div v-if="employerStore.applicationsPagination && employerStore.applicationsPagination.last_page > 1"
                class="flex justify-center gap-2 pt-4">
                <button
                    v-for="page in employerStore.applicationsPagination.last_page"
                    :key="page"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-sm font-medium',
                        page === employerStore.applicationsPagination!.current_page
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
