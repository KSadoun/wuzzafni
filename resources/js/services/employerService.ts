import { fetchApi } from '@/utils/fetchApi';
import type { EmployerApplication, JobAnalytics, PaginatedResponse, PaymentDetails, Job } from '@/types/api';

export const employerService = {
    async getJobs(page: number = 1): Promise<PaginatedResponse<Job>> {
        return fetchApi(`/employer/jobs?page=${page}`);
    },

    async getJobApplications(jobId: number | string, page: number = 1): Promise<PaginatedResponse<EmployerApplication>> {
        return fetchApi(`/employer/jobs/${jobId}/applications?page=${page}`);
    },

    async acceptApplication(applicationId: number | string): Promise<{
        message: string;
        application: EmployerApplication;
        payment: PaymentDetails;
    }> {
        return fetchApi(`/employer/applications/${applicationId}/accept`, { method: 'POST' });
    },

    async getPaymentDetails(applicationId: number | string): Promise<{
        application: EmployerApplication;
        payment: PaymentDetails;
        amount: number;
        currency: string;
    }> {
        return fetchApi(`/employer/applications/${applicationId}/payment`);
    },
};

export const paymentService = {
    async createPayPalOrder(applicationId: number | string): Promise<{
        status: string;
        order_id: string;
        approval_url: string;
        amount: string;
        currency: string;
    }> {
        return fetchApi(`/paypal/payment?application_id=${applicationId}`);
    },

    async capturePayment(applicationId: number | string, token: string): Promise<{
        status: string;
        message: string;
        payment: PaymentDetails;
    }> {
        return fetchApi(`/paypal/success?application_id=${applicationId}&token=${encodeURIComponent(token)}`);
    },
};

export const analyticsService = {
    async getJobAnalytics(jobId: number | string): Promise<JobAnalytics> {
        return fetchApi(`/jobs/${jobId}/analytics`);
    },
};
