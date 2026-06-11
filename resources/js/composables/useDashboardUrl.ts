import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import candidate from '@/routes/candidate';
import { dashboard } from '@/routes';

export function useDashboardUrl() {
    const page = usePage();

    return computed(() => {
        const sharedUrl = (page.props as { dashboardUrl?: string }).dashboardUrl;
        if (sharedUrl) {
            return sharedUrl;
        }

        const role = (page.props.auth as { user?: { role?: string } })?.user?.role;

        if (role === 'employer') {
            return '/employer/jobs';
        }

        if (role === 'candidate') {
            return dashboard();
        }

        return dashboard();
    });
}
