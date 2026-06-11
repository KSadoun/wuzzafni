import type { Application, CandidateDashboard, PaginatedResponse } from '@/types/api';
import { fetchApi } from '@/utils/fetchApi';

export const applicationService = {
    async getDashboard(): Promise<CandidateDashboard> {
        return fetchApi('/candidate/dashboard', { method: 'GET' });
    },

    async apply(jobId: number | string, data: FormData | { use_existing_resume: boolean; cover_letter?: string; phone?: string }): Promise<{ message: string; application?: Application }> {
        // If data is FormData, send it as body directly
        if (data instanceof FormData) {
            return fetchApi(`/jobs/${jobId}/apply`, {
                method: 'POST',
                body: data
            });
        }
        
        // Otherwise, it's JSON
        return fetchApi(`/jobs/${jobId}/apply`, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });
    },

    async getApplications(page: number = 1): Promise<PaginatedResponse<Application>> {
        return fetchApi(`/candidate/applications?page=${page}`, { method: 'GET' });
    },

    async getApplication(id: number | string): Promise<{ data: Application }> {
        return fetchApi(`/candidate/applications/${id}`, { method: 'GET' });
    },

    async cancelApplication(id: number | string): Promise<{ message: string }> {
        return fetchApi(`/candidate/applications/${id}/cancel`, {
            method: 'POST'
        });
    }
};

