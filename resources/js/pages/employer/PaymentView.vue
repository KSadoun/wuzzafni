<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { employerService, paymentService } from '@/services/employerService';
import type { EmployerApplication, PaymentDetails } from '@/types/api';
import { Loader2, ArrowLeft, CreditCard, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{ applicationId: string }>();

const application = ref<EmployerApplication | null>(null);
const payment = ref<PaymentDetails | null>(null);
const amount = ref<number | string>(0);
const currency = ref('USD');
const isLoading = ref(true);
const isPaying = ref(false);
const error = ref<string | null>(null);

onMounted(async () => {
    try {
        const data = await employerService.getPaymentDetails(props.applicationId);
        application.value = data.application;
        payment.value = data.payment;
        amount.value = data.amount;
        currency.value = data.currency;
    } catch (err: any) {
        error.value = err.message || 'Failed to load payment details.';
    } finally {
        isLoading.value = false;
    }
});

async function handlePayWithPayPal() {
    if (payment.value?.payment_status === 'paid') {
        return;
    }

    isPaying.value = true;
    try {
        const result = await paymentService.createPayPalOrder(props.applicationId);
        window.location.href = result.approval_url;
    } catch (err: any) {
        toast.error(err.message || 'Failed to initiate PayPal payment.');
        isPaying.value = false;
    }
}
</script>

<template>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <RouterLink
            v-if="application?.job_id"
            :to="{ name: 'employer.job-applications', params: { jobId: application.job_id } }"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors font-medium mb-6">
            <ArrowLeft class="w-4 h-4" />
            Back to Applications
        </RouterLink>

        <div v-if="isLoading" class="flex justify-center py-24">
            <Loader2 class="w-10 h-10 text-emerald-500 animate-spin" />
        </div>

        <div v-else-if="error" class="bg-red-50 text-red-600 p-8 rounded-xl text-center border border-red-100">
            <AlertCircle class="w-12 h-12 mx-auto mb-4 opacity-50" />
            {{ error }}
        </div>

        <div v-else-if="application" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-emerald-600 px-6 py-8 text-white text-center">
                <CreditCard class="w-12 h-12 mx-auto mb-3 opacity-90" />
                <h1 class="text-2xl font-bold">Complete Hiring Payment</h1>
                <p class="text-emerald-100 mt-1">Finalize your candidate acceptance</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="border-b border-gray-100 pb-6">
                    <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Order Summary</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Job</span>
                            <span class="font-medium text-gray-900">{{ application.job.title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Candidate</span>
                            <span class="font-medium text-gray-900">{{ application.candidate.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Experience Level</span>
                            <span class="font-medium text-gray-900 capitalize">{{ application.job.experience_level || 'Standard' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center py-4 bg-gray-50 rounded-xl px-4">
                    <span class="text-lg font-semibold text-gray-900">Total Due</span>
                    <span class="text-2xl font-bold text-emerald-600">
                        {{ currency }} {{ Number(amount).toFixed(2) }}
                    </span>
                </div>

                <div v-if="payment?.payment_status === 'paid'"
                    class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800">
                    <CheckCircle class="w-5 h-5 shrink-0" />
                    <p class="text-sm font-medium">Payment completed successfully.</p>
                </div>

                <button
                    v-else
                    :disabled="isPaying"
                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-[#0070ba] hover:bg-[#003087] text-white font-semibold rounded-xl transition disabled:opacity-50"
                    @click="handlePayWithPayPal">
                    <Loader2 v-if="isPaying" class="w-5 h-5 animate-spin" />
                    <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72a.77.77 0 0 1 .762-.658h6.988c2.372 0 4.076.608 5.066 1.807.907 1.09 1.19 2.507.842 4.218-.683 3.448-3.012 5.456-6.828 5.456H9.314l-.923 5.794z"/>
                    </svg>
                    Pay with PayPal
                </button>

                <p class="text-xs text-gray-400 text-center">
                    You will be redirected to PayPal to complete your payment securely.
                </p>
            </div>
        </div>
    </div>
</template>
