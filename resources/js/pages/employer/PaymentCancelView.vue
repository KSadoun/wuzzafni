<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { XCircle } from 'lucide-vue-next';

const props = defineProps<{ applicationId: string }>();
</script>

<template>
    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div class="bg-white border border-gray-200 rounded-2xl p-10 shadow-sm">
            <XCircle class="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Cancelled</h1>
            <p class="text-gray-600 mb-8">
                You cancelled the PayPal payment. The candidate acceptance is saved, but payment is still pending.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <RouterLink
                    :to="{ name: 'employer.payment', params: { applicationId: props.applicationId } }"
                    class="inline-flex px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition">
                    Retry Payment
                </RouterLink>
                <RouterLink
                    :to="{ name: 'employer.jobs' }"
                    class="inline-flex px-6 py-3 bg-white text-gray-700 font-medium rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                    Back to Jobs
                </RouterLink>
            </div>
        </div>
    </div>
</template>
