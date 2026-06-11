<script setup lang="ts">
import { MapPin, Briefcase, DollarSign, Clock } from 'lucide-vue-next';

defineProps<{
    job: Record<string, any>;
}>();
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div class="flex gap-4">
                <img v-if="job.employer?.logo" :src="job.employer.logo" alt="Company Logo" class="w-12 h-12 rounded-lg object-cover" />
                <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xl">
                    {{ job.employer?.company_name?.charAt(0) || 'C' }}
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                        {{ job.title }}
                    </h3>
                    <p class="text-gray-600 mt-1">{{ job.employer?.company_name || 'Confidential Company' }}</p>
                </div>
            </div>
            
            <span v-if="job.work_type" class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                {{ job.work_type }}
            </span>
        </div>

        <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-500">
            <div class="flex items-center gap-1.5">
                <MapPin class="w-4 h-4" />
                <span>{{ job.location || 'Remote' }}</span>
            </div>
            <div class="flex items-center gap-1.5" v-if="job.salary_min && job.salary_max">
                <DollarSign class="w-4 h-4" />
                <span>${{ job.salary_min }} - ${{ job.salary_max }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <Briefcase class="w-4 h-4" />
                <span>{{ job.experience_level || 'Any' }}</span>
            </div>
            <div class="flex items-center gap-1.5" v-if="job.created_at">
                <Clock class="w-4 h-4" />
                <span>Posted {{ new Date(job.created_at).toLocaleDateString() }}</span>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2" v-if="job.technologies?.length">
            <span v-for="tech in job.technologies.slice(0, 3)" :key="tech.id" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">
                {{ tech.name }}
            </span>
            <span v-if="job.technologies.length > 3" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">
                +{{ job.technologies.length - 3 }} more
            </span>
        </div>
    </div>
</template>
