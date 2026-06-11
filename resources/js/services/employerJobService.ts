import { fetchApi } from '@/utils/fetchApi';
import type { Job, PaginatedResponse } from '@/types/api';

export interface EmployerJobInput {
    title: string;
    description: string;
    responsibilities?: string;
    requirements?: string;
    benefits?: string;
    salary_min?: number | null;
    salary_max?: number | null;
    location?: string;
    work_type: 'remote' | 'onsite' | 'hybrid';
    experience_level?: 'entry' | 'mid' | 'senior' | 'lead' | null;
    application_deadline?: string | null;
    status: 'active' | 'closed' | 'draft';
    category_ids?: number[];
    technology_ids?: number[];
}

export const employerJobService = {
    async getJobs(page = 1): Promise<PaginatedResponse<Job>> {
        return fetchApi(`/employer/jobs?page=${page}`, { method: 'GET' });
    },

    async getJob(id: number | string): Promise<{ data: Job }> {
        return fetchApi(`/employer/jobs/${id}`, { method: 'GET' });
    },

    async createJob(data: EmployerJobInput): Promise<{ message: string; job: Job }> {
        return fetchApi('/employer/jobs', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },

    async updateJob(id: number | string, data: EmployerJobInput): Promise<{ message: string; job: Job }> {
        return fetchApi(`/employer/jobs/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    },

    async deleteJob(id: number | string): Promise<{ message: string }> {
        return fetchApi(`/employer/jobs/${id}`, { method: 'DELETE' });
    },

    async getMetaOptions(): Promise<{ categories: { id: number; name: string }[]; technologies: { id: number; name: string }[] }> {
        return fetchApi('/meta/job-options', { method: 'GET' });
    }
};
