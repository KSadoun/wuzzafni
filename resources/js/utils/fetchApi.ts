import { AppApiError } from '@/types/api';

function getCsrfToken(): string {
    // Read the XSRF-TOKEN cookie that Laravel sets automatically
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

export async function fetchApi(endpoint: string, options: RequestInit = {}) {
    const method = (options.method || 'GET').toUpperCase();

    const defaultHeaders: Record<string, string> = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    // Include CSRF token for state-mutating requests
    if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
        const csrfToken = getCsrfToken();

        if (csrfToken) {
            defaultHeaders['X-XSRF-TOKEN'] = csrfToken;
        }
    }

    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
    }
    
    const response = await fetch(`/api${endpoint}`, {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers,
        },
        credentials: 'same-origin',
    });


    if (!response.ok) {
        const text = await response.text();
        let errorData;

        try {
            errorData = JSON.parse(text);
        } catch {
            console.error('API Error (not JSON):', text);
            errorData = { message: response.statusText || 'Server returned an invalid response format.' };
        }
        
        let message = errorData.message || 'An unexpected error occurred.';
        
        if (response.status === 401) {
            message = 'You are unauthenticated. Please log in.';
        } else if (response.status === 403) {
            message = errorData.message || 'You do not have permission to perform this action.';
        } else if (response.status === 404) {
            message = 'The requested resource was not found.';
        } else if (response.status === 422) {
            // Extract first validation error message
            if (errorData.errors) {
                const firstKey = Object.keys(errorData.errors)[0];
                message = errorData.errors[firstKey]?.[0] || errorData.message;
            }
        } else if (response.status === 500) {
            message = 'A server error occurred. Please try again later.';
        }

        throw new AppApiError(message, response.status, errorData.errors);
    }

    if (response.status === 204) {
return null;
}
    
    return response.json();
}

