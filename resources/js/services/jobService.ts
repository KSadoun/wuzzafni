import type { Job, PaginatedResponse } from '@/types/api';
import { fetchApi } from '@/utils/fetchApi';

export const jobService = {
    async getJobs(params: Record<string, any> = {}): Promise<PaginatedResponse<Job>> {
        const urlParams = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                // If it's an array (like multiple categories), we append them correctly
                if (Array.isArray(value)) {
                    value.forEach(v => urlParams.append(`${key}[]`, String(v)));
                } else {
                    urlParams.append(key, String(value));
                }
            }
        });
        const qs = urlParams.toString();
        const endpoint = qs ? `/jobs?${qs}` : '/jobs';

        return fetchApi(endpoint, { method: 'GET' });
    },

    async getJob(id: number | string): Promise<{ data: Job }> {
        return fetchApi(`/jobs/${id}`, { method: 'GET' });
    }
};

