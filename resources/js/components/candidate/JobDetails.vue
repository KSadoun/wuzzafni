<script setup lang="ts">
import { MapPin, Briefcase, DollarSign, Clock, Building } from 'lucide-vue-next';

defineProps<{
    job: Record<string, any>;
}>();
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="p-8 border-b border-gray-100">
            <div class="flex items-start gap-6">
                <img v-if="job.employer?.logo" :src="job.employer.logo" alt="Logo" class="w-20 h-20 rounded-xl object-cover border border-gray-100 shadow-sm" />
                <div v-else class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-3xl shadow-sm">
                    {{ job.employer?.company_name?.charAt(0) || 'C' }}
                </div>
                
                <div class="flex-grow">
                    <h1 class="text-3xl font-bold text-gray-900">{{ job.title }}</h1>
                    <p class="text-xl text-blue-600 font-medium mt-1">{{ job.employer?.company_name || 'Confidential Company' }}</p>
                    
                    <div class="mt-4 flex flex-wrap gap-4 text-gray-600">
                        <div class="flex items-center gap-2">
                            <MapPin class="w-5 h-5 text-gray-400" />
                            <span>{{ job.location || 'Remote' }}</span>
                        </div>
                        <div class="flex items-center gap-2" v-if="job.salary_min && job.salary_max">
                            <DollarSign class="w-5 h-5 text-gray-400" />
                            <span>${{ job.salary_min }} - ${{ job.salary_max }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Briefcase class="w-5 h-5 text-gray-400" />
                            <span>{{ job.work_type }} • {{ job.experience_level || 'Any Experience' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8">
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Job Description</h3>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-8">{{ job.description }}</p>

                <template v-if="job.responsibilities">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Responsibilities</h3>
                    <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-8">{{ job.responsibilities }}</p>
                </template>

                <template v-if="job.requirements">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Requirements</h3>
                    <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-8">{{ job.requirements }}</p>
                </template>

                <template v-if="job.benefits">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Benefits</h3>
                    <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-8">{{ job.benefits }}</p>
                </template>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-100" v-if="job.technologies?.length || job.categories?.length">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills & Categories</h3>
                <div class="flex flex-wrap gap-2">
                    <span v-for="cat in job.categories" :key="'cat-'+cat.id" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 font-medium text-sm rounded-lg">
                        {{ cat.name }}
                    </span>
                    <span v-for="tech in job.technologies" :key="'tech-'+tech.id" class="px-3 py-1.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-lg">
                        {{ tech.name }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
