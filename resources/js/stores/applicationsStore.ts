import { defineStore } from 'pinia';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { applicationService } from '@/services/applicationService';
import type { Application, PaginatedResponse } from '@/types/api';

export const useApplicationsStore = defineStore('applications', () => {
    const applications = ref<Application[]>([]);
    const pagination = ref<PaginatedResponse<Application>['meta'] | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchApplications(page = 1) {
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

            const response = await applicationService.getApplications(page);
            applications.value = response.data;
            pagination.value = response.meta || null;
        } catch (err: any) {
            error.value = err.message || 'Failed to load applications';
            toast.error(error.value as string);
        } finally {
            isLoading.value = false;
        }
    }

    async function applyForJob(jobId: number | string, formData: FormData | { use_existing_resume: boolean; cover_letter?: string; phone?: string }) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await applicationService.apply(jobId, formData);

            if (response.application) {
                applications.value.unshift(response.application);
            }

            toast.success(response.message || 'Successfully applied for the job!');

            return response.application;
        } catch (err: any) {
            error.value = err.message || 'Application failed';
            toast.error(error.value as string);

            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function cancelApplication(id: number | string) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await applicationService.cancelApplication(id);
            // We should re-fetch or simply update local status if backend doesn't return full object
            // For now, let's just refetch applications to ensure state consistency
            await fetchApplications(pagination.value?.current_page || 1);
            toast.success(response.message || 'Application cancelled successfully');
        } catch (err: any) {
            error.value = err.message || 'Failed to cancel application';
            toast.error(error.value as string);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        applications,
        pagination,
        isLoading,
        error,
        fetchApplications,
        applyForJob,
        cancelApplication
    };
});

