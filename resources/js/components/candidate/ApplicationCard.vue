<script setup lang="ts">
import { Briefcase, Building, DollarSign, Calendar } from 'lucide-vue-next';

defineProps<{
    application: Record<string, any>;
}>();

const emit = defineEmits<{
    (e: 'cancel', id: number): void;
}>();

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'accepted': return 'bg-green-100 text-green-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'cancelled': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
        <div class="p-6 flex-grow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ application.job?.title || 'Unknown Job' }}</h3>
                    <p class="text-gray-600 flex items-center gap-2 mt-1">
                        <Building class="w-4 h-4" />
                        {{ application.job?.employerProfile?.company_name || 'Confidential Company' }}
                    </p>
                </div>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold capitalize', getStatusColor(application.status)]">
                    {{ application.status }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
                <div class="flex items-center gap-2">
                    <Calendar class="w-4 h-4 text-gray-400" />
                    <span>Applied on: {{ new Date(application.applied_at).toLocaleDateString() }}</span>
                </div>
                <div class="flex items-center gap-2" v-if="application.resume_url">
                    <Briefcase class="w-4 h-4 text-gray-400" />
                    <a :href="application.resume_url" target="_blank" class="text-blue-600 hover:underline">View Submitted Resume</a>
                </div>
                <div class="flex items-center gap-2">
                    <DollarSign class="w-4 h-4 text-gray-400" />
                    <span>Contact: {{ application.email }}</span>
                </div>
            </div>

        </div>
        
        <div class="bg-gray-50 border-t md:border-t-0 md:border-l border-gray-200 p-6 flex flex-col justify-center items-center md:items-end gap-3 md:w-48 shrink-0">
            <button 
                v-if="application.status === 'pending'"
                @click="emit('cancel', application.id)"
                class="w-full px-4 py-2 bg-white border border-red-200 text-red-600 font-medium rounded-lg hover:bg-red-50 focus:ring-4 focus:ring-red-100 transition-colors"
            >
                Cancel Application
            </button>
            <p v-else class="text-sm text-gray-500 text-center md:text-right">
                No further actions available
            </p>
        </div>
    </div>
</template>
