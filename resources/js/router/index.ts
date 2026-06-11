import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/employer',
        redirect: '/employer/jobs',
    },
    {
        path: '/employer/jobs',
        name: 'employer.jobs',
        component: () => import('@/pages/employer/JobsView.vue'),
        meta: { title: 'My Jobs' },
    },
    {
        path: '/employer/jobs/:jobId/applications',
        name: 'employer.job-applications',
        component: () => import('@/pages/employer/JobApplicationsView.vue'),
        props: true,
        meta: { title: 'Applications' },
    },
    {
        path: '/employer/jobs/:jobId/analytics',
        name: 'employer.job-analytics',
        component: () => import('@/pages/employer/JobAnalyticsView.vue'),
        props: true,
        meta: { title: 'Job Analytics' },
    },
    {
        path: '/employer/applications/:applicationId/payment',
        name: 'employer.payment',
        component: () => import('@/pages/employer/PaymentView.vue'),
        props: true,
        meta: { title: 'Payment' },
    },
    {
        path: '/employer/applications/:applicationId/payment/success',
        name: 'employer.payment.success',
        component: () => import('@/pages/employer/PaymentSuccessView.vue'),
        props: true,
        meta: { title: 'Payment Success' },
    },
    {
        path: '/employer/applications/:applicationId/payment/cancel',
        name: 'employer.payment.cancel',
        component: () => import('@/pages/employer/PaymentCancelView.vue'),
        props: true,
        meta: { title: 'Payment Cancelled' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
