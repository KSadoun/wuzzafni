import { defineStore } from 'pinia';
import { ref } from 'vue';
import { jobService } from '@/services/jobService';
import type { Job, PaginatedResponse } from '@/types/api';

export const useJobsStore = defineStore('jobs', () => {
    const jobs = ref<Job[]>([]);
    const pagination = ref<PaginatedResponse<Job>['meta'] | null>(null);
    const currentJob = ref<Job | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    
    // Default filters
    const filters = ref<Record<string, any>>({
        keyword: '',
        location: '',
        category: [],
        technologies: [],
        work_type: '',
        salary_min: '',
        salary_max: '',
        date_posted: '',
        page: 1
    });

    async function fetchJobs() {
        isLoading.value = true;
        error.value = null;
        try {
            // Sync filters with browser URL query parameters without reloading
            if (typeof window !== 'undefined') {
                const urlParams = new URLSearchParams();
                Object.entries(filters.value).forEach(([key, value]) => {
                    if (value !== undefined && value !== null && value !== '') {
                        if (Array.isArray(value)) {
                            value.forEach(v => urlParams.append(`${key}[]`, String(v)));
                        } else {
                            urlParams.append(key, String(value));
                        }
                    }
                });
                const qs = urlParams.toString();
                const newUrl = qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // API returns: { data: Job[], meta: {}, links: {} }
            const response = await jobService.getJobs(filters.value);
            jobs.value = response.data;
            pagination.value = response.meta || null;
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch jobs.';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchJobDetails(id: string | number) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await jobService.getJob(id);
            currentJob.value = response.data;
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch job details.';
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    function setFilters(newFilters: Record<string, any>) {
        filters.value = { ...filters.value, ...newFilters, page: 1 };
        fetchJobs();
    }
    
    function setPage(page: number) {
        filters.value.page = page;
        fetchJobs();
    }

    return {
        jobs,
        pagination,
        currentJob,
        filters,
        isLoading,
        error,
        fetchJobs,
        fetchJobDetails,
        setFilters,
        setPage
    };
});

