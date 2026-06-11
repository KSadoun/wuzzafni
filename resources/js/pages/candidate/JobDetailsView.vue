<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, AlertCircle } from 'lucide-vue-next';
import { onMounted, computed, ref } from 'vue';
import ApplyModal from '@/components/candidate/ApplyModal.vue';
import JobDetails from '@/components/candidate/JobDetails.vue';
import { useApplicationsStore } from '@/stores/applicationsStore';
import { useJobsStore } from '@/stores/jobsStore';

const props = defineProps<{
    jobId: number | string;
}>();

const jobsStore = useJobsStore();
const applicationsStore = useApplicationsStore();

const isApplying = ref(false);

onMounted(() => {
    jobsStore.fetchJobDetails(props.jobId);
});

const job = computed(() => jobsStore.currentJob);

const hasApplied = computed(() => {
    if (job.value?.has_applied) {
return true;
}

    return applicationsStore.applications.some(app => app.job_id == props.jobId);
});

const hasPassedDeadline = computed(() => {
    if (!job.value?.application_deadline) {
return false;
}

    return new Date(job.value.application_deadline) < new Date();
});

const isClosed = computed(() => {
    return job.value?.status === 'closed' || job.value?.status === 'draft';
});

async function handleApply(formData: FormData | { use_existing_resume: boolean; cover_letter?: string; phone?: string; email?: string }) {
    try {
        isApplying.value = true;
        await applicationsStore.applyForJob(props.jobId, formData);
        // Refresh job details to update applications count and has_applied status
        jobsStore.fetchJobDetails(props.jobId);
    } finally {
        isApplying.value = false;
    }
}
</script>

<template>
    <Head :title="job ? job.title : 'Job Details'" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <Link href="/jobs" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors font-medium">
                <ArrowLeft class="w-4 h-4" />
                Back to Jobs
            </Link>
        </div>

        <div v-if="jobsStore.isLoading" class="flex justify-center items-center py-32">
            <Loader2 class="w-12 h-12 text-blue-600 animate-spin" />
        </div>
        
        <div v-else-if="jobsStore.error" class="bg-red-50 text-red-600 p-8 rounded-xl text-center border border-red-100">
            <AlertCircle class="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p class="text-lg font-medium">{{ jobsStore.error }}</p>
        </div>

        <div v-else-if="job">
            <!-- Apply Actions Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex items-center justify-between sticky top-4 z-10">
                <div>
                    <h2 class="font-semibold text-gray-900 truncate pr-4">{{ job.title }}</h2>
                    <p class="text-sm text-gray-500">{{ job.employer?.company_name }}</p>
                </div>
                
                <div>
                    <button v-if="hasApplied" disabled class="px-6 py-2.5 bg-gray-100 text-gray-500 font-medium rounded-lg border border-gray-200 cursor-not-allowed">
                        Already Applied
                    </button>
                    <button v-else-if="isClosed" disabled class="px-6 py-2.5 bg-gray-100 text-gray-500 font-medium rounded-lg border border-gray-200 cursor-not-allowed">
                        Job Closed
                    </button>
                    <button v-else-if="hasPassedDeadline" disabled class="px-6 py-2.5 bg-red-50 text-red-500 font-medium rounded-lg border border-red-100 cursor-not-allowed">
                        Deadline Passed
                    </button>
                    <ApplyModal 
                        v-else 
                        :jobId="job.id" 
                        @apply="handleApply"
                    >
                        <template #trigger>
                            <button :disabled="isApplying" class="px-8 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors shadow-sm flex items-center gap-2">
                                <Loader2 v-if="isApplying" class="w-4 h-4 animate-spin" />
                                Apply Now
                            </button>
                        </template>
                    </ApplyModal>
                </div>
            </div>

            <!-- Job Details Core -->
            <JobDetails :job="job" />
        </div>
    </div>
</template>

