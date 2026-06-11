<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { paymentService } from '@/services/employerService';
import { Loader2, CheckCircle, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{ applicationId: string }>();

const route = useRoute();
const isProcessing = ref(true);
const success = ref(false);
const message = ref('');

onMounted(async () => {
    const token = route.query.token as string;

    if (!token) {
        success.value = false;
        message.value = 'Missing payment token from PayPal.';
        isProcessing.value = false;
        return;
    }

    try {
        const result = await paymentService.capturePayment(props.applicationId, token);
        success.value = result.status === 'COMPLETED';
        message.value = result.message;
    } catch (err: any) {
        success.value = false;
        message.value = err.message || 'Payment verification failed.';
    } finally {
        isProcessing.value = false;
    }
});
</script>

<template>
    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div v-if="isProcessing" class="flex flex-col items-center gap-4">
            <Loader2 class="w-12 h-12 text-emerald-500 animate-spin" />
            <p class="text-gray-600">Verifying your payment with PayPal…</p>
        </div>

        <div v-else-if="success" class="bg-white border border-green-200 rounded-2xl p-10 shadow-sm">
            <CheckCircle class="w-16 h-16 text-green-500 mx-auto mb-4" />
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-600 mb-8">{{ message }}</p>
            <RouterLink
                :to="{ name: 'employer.jobs' }"
                class="inline-flex px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition">
                Back to My Jobs
            </RouterLink>
        </div>

        <div v-else class="bg-white border border-red-200 rounded-2xl p-10 shadow-sm">
            <AlertCircle class="w-16 h-16 text-red-400 mx-auto mb-4" />
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
            <p class="text-gray-600 mb-8">{{ message }}</p>
            <RouterLink
                :to="{ name: 'employer.payment', params: { applicationId: props.applicationId } }"
                class="inline-flex px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition">
                Try Again
            </RouterLink>
        </div>
    </div>
</template>
