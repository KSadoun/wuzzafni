import { defineStore } from 'pinia';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { employerJobService  } from '@/services/employerJobService';
import type {EmployerJobInput} from '@/services/employerJobService';
import type { Job, PaginatedResponse } from '@/types/api';

export const useEmployerJobsStore = defineStore('employerJobs', () => {
    const jobs = ref<Job[]>([]);
    const pagination = ref<PaginatedResponse<Job>['meta'] | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchJobs(page = 1) {
        isLoading.value = true;
        error.value = null;

        try {
            // Sync page with browser URL query parameters without reloading
            if (typeof window !== 'undefined') {
                const urlParams = new URLSearchParams(window.location.search);

                if (page > 1) {
                    urlParams.set('page', String(page));
                } else {
                    urlParams.delete('page');
                }

                const qs = urlParams.toString();
                const newUrl = qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            const response = await employerJobService.getJobs(page);
            jobs.value = response.data;
            pagination.value = response.meta || null;
        } catch (err: any) {
            error.value = err.message || 'Failed to load jobs';
            toast.error(error.value as string);
        } finally {
            isLoading.value = false;
        }
    }

    async function createJob(data: EmployerJobInput) {
        isLoading.value = true;

        try {
            const response = await employerJobService.createJob(data);
            toast.success(response.message || 'Job posted successfully!');

            return response.job;
        } catch (err: any) {
            toast.error(err.message || 'Failed to post job');

            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updateJob(id: number | string, data: EmployerJobInput) {
        isLoading.value = true;

        try {
            const response = await employerJobService.updateJob(id, data);
            toast.success(response.message || 'Job updated successfully!');

            return response.job;
        } catch (err: any) {
            toast.error(err.message || 'Failed to update job');

            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function deleteJob(id: number | string) {
        isLoading.value = true;

        try {
            const response = await employerJobService.deleteJob(id);
            jobs.value = jobs.value.filter(j => j.id !== id);
            toast.success(response.message || 'Job deleted successfully!');
        } catch (err: any) {
            toast.error(err.message || 'Failed to delete job');

            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        jobs,
        pagination,
        isLoading,
        error,
        fetchJobs,
        createJob,
        updateJob,
        deleteJob
    };
});
