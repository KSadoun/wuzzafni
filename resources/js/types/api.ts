export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status?: number;
}

export class AppApiError extends Error {
    public status?: number;
    public errors?: Record<string, string[]>;

    constructor(message: string, status?: number, errors?: Record<string, string[]>) {
        super(message);
        this.name = 'AppApiError';
        this.status = status;
        this.errors = errors;
    }
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
    links?: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}

export interface Job {
    id: number;
    title: string;
    description: string;
    responsibilities?: string;
    requirements?: string;
    benefits?: string;
    salary_min?: number;
    salary_max?: number;
    location?: string;
    work_type: string;
    experience_level?: string;
    application_deadline?: string;
    status: string;
    views_count: number;
    applications_count: number;
    employer: {
        id: number;
        company_name: string;
        company_description?: string;
        company_logo?: string;
        location?: string;
    };
    categories: { id: number; name: string }[];
    technologies: { id: number; name: string }[];
    created_at: string;
    has_applied: boolean; // Computed by backend if the user is authenticated
}

export interface CandidateDashboard {
    stats: {
        total: number;
        pending: number;
        reviewed: number;
        accepted: number;
        rejected: number;
    };
    recent_applications: Application[];
}

export interface Application {
    id: number;
    job_id: number;
    job: Job;
    status: string; // 'pending', 'reviewed', 'accepted', 'rejected', 'canceled'
    applied_at: string;
    created_at: string;
}

export interface EmployerApplication {
    id: number;
    job_id: number;
    job: {
        id: number;
        title: string;
        experience_level?: string;
    };
    candidate: {
        name?: string;
        email?: string;
        phone?: string;
        experience_level?: string;
        current_position?: string;
    };
    resume_url?: string | null;
    cover_letter?: string;
    status: string;
    applied_at: string;
    payment?: PaymentDetails;
}

export interface PaymentDetails {
    id: number;
    amount: number | string;
    payment_status: string;
    payment_method: string;
    transaction_id?: string | null;
}

export interface JobAnalytics {
    job: {
        id: number;
        title: string;
    };
    total_applications: number;
    applications_per_day: number;
    pending: number;
    reviewed: number;
    accepted: number;
    rejected: number;
}
