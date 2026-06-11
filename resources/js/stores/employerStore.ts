import { defineStore } from 'pinia';
import { ref } from 'vue';
import { employerService } from '@/services/employerService';
import type { EmployerApplication, Job, PaginatedResponse } from '@/types/api';

export const useEmployerStore = defineStore('employer', () => {
    const jobs = ref<Job[]>([]);
    const applications = ref<EmployerApplication[]>([]);
    const pagination = ref<PaginatedResponse<Job>['meta'] | null>(null);
    const applicationsPagination = ref<PaginatedResponse<EmployerApplication>['meta'] | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchJobs(page: number = 1) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await employerService.getJobs(page);
            jobs.value = response.data;
            pagination.value = response.meta || null;
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch jobs.';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchApplications(jobId: number | string, page: number = 1) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await employerService.getJobApplications(jobId, page);
            applications.value = response.data;
            applicationsPagination.value = response.meta || null;
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch applications.';
        } finally {
            isLoading.value = false;
        }
    }

    async function acceptApplication(applicationId: number | string) {
        return employerService.acceptApplication(applicationId);
    }

    return {
        jobs,
        applications,
        pagination,
        applicationsPagination,
        isLoading,
        error,
        fetchJobs,
        fetchApplications,
        acceptApplication,
    };
});
