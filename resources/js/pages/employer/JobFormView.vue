<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { toast } from 'vue-sonner';
import { 
    Loader2, 
    ArrowLeft, 
    Briefcase, 
    MapPin, 
    Calendar, 
    DollarSign, 
    Layers, 
    Cpu, 
    FileText, 
    Check 
} from 'lucide-vue-next';
import { useEmployerJobsStore } from '@/stores/employerJobsStore';
import { employerJobService } from '@/services/employerJobService';

const props = defineProps<{
    jobId?: string | number;
}>();

const employerJobsStore = useEmployerJobsStore();

// Page state
const isEditMode = computed(() => !!props.jobId);
const isMetadataLoading = ref(true);
const isSubmitting = ref(false);

// Lists for tags
const categoriesList = ref<{ id: number; name: string }[]>([]);
const technologiesList = ref<{ id: number; name: string }[]>([]);

// Form State
const form = ref({
    title: '',
    description: '',
    responsibilities: '',
    requirements: '',
    benefits: '',
    salary_min: null as number | null,
    salary_max: null as number | null,
    location: '',
    work_type: 'remote' as 'remote' | 'onsite' | 'hybrid',
    experience_level: 'mid' as 'entry' | 'mid' | 'senior' | 'lead',
    application_deadline: '',
    status: 'active' as 'active' | 'closed' | 'draft',
    category_ids: [] as number[],
    technology_ids: [] as number[],
});

// Validation errors
const errors = ref<Record<string, string>>({});

onMounted(async () => {
    try {
        isMetadataLoading.value = true;
        
        // Fetch categories & technologies
        const meta = await employerJobService.getMetaOptions();
        categoriesList.value = meta.categories;
        technologiesList.value = meta.technologies;

        // If in edit mode, fetch the job details
        if (isEditMode.value && props.jobId) {
            const response = await employerJobService.getJob(props.jobId);
            const job = response.data;
            
            form.value = {
                title: job.title || '',
                description: job.description || '',
                responsibilities: job.responsibilities || '',
                requirements: job.requirements || '',
                benefits: job.benefits || '',
                salary_min: job.salary_min ? Math.round(Number(job.salary_min)) : null,
                salary_max: job.salary_max ? Math.round(Number(job.salary_max)) : null,
                location: job.location || '',
                work_type: (job.work_type as any) || 'remote',
                experience_level: (job.experience_level as any) || 'mid',
                application_deadline: job.application_deadline || '',
                status: (job.status as any) || 'active',
                category_ids: job.categories?.map(c => c.id) || [],
                technology_ids: job.technologies?.map(t => t.id) || [],
            };
        }
    } catch (err: any) {
        toast.error(err.message || 'Failed to initialize form.');
        router.visit('/employer/jobs');
    } finally {
        isMetadataLoading.value = false;
    }
});

// Category and Technology toggle helpers
function toggleCategory(id: number) {
    const idx = form.value.category_ids.indexOf(id);

    if (idx >= 0) {
        form.value.category_ids.splice(idx, 1);
    } else {
        form.value.category_ids.push(id);
    }
}

function toggleTechnology(id: number) {
    const idx = form.value.technology_ids.indexOf(id);

    if (idx >= 0) {
        form.value.technology_ids.splice(idx, 1);
    } else {
        form.value.technology_ids.push(id);
    }
}

// Client-side validation
function validateForm() {
    const tempErrors: Record<string, string> = {};

    if (!form.value.title.trim()) {
tempErrors.title = 'Job title is required.';
}

    if (!form.value.description.trim()) {
tempErrors.description = 'Job description is required.';
}
    
    if (form.value.salary_min !== null && form.value.salary_min < 0) {
        tempErrors.salary_min = 'Salary minimum cannot be negative.';
    }

    if (form.value.salary_max !== null && form.value.salary_max < 0) {
        tempErrors.salary_max = 'Salary maximum cannot be negative.';
    }

    if (
        form.value.salary_min !== null && 
        form.value.salary_max !== null && 
        Number(form.value.salary_max) < Number(form.value.salary_min)
    ) {
        tempErrors.salary_max = 'Maximum salary must be greater than or equal to minimum salary.';
    }

    if (form.value.application_deadline) {
        const deadlineDate = new Date(form.value.application_deadline);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (deadlineDate <= today) {
            tempErrors.application_deadline = 'Deadline must be a future date.';
        }
    }

    errors.value = tempErrors;

    return Object.keys(tempErrors).length === 0;
}

// Form Submission
async function handleSubmit() {
    if (!validateForm()) {
        toast.error('Please fix the errors in the form.');

        return;
    }

    isSubmitting.value = true;

    try {
        const payload = {
            ...form.value,
            // Format application deadline for Laravel validation if present
            application_deadline: form.value.application_deadline || null,
        };

        if (isEditMode.value && props.jobId) {
            await employerJobsStore.updateJob(props.jobId, payload as any);
        } else {
            await employerJobsStore.createJob(payload as any);
        }
        
        router.visit('/employer/jobs');
    } catch (err: any) {
        if (err.errors) {
            // Map Laravel validation errors to our local state
            const serverErrors: Record<string, string> = {};
            Object.entries(err.errors).forEach(([key, messages]: any) => {
                serverErrors[key] = messages[0];
            });
            errors.value = serverErrors;
            toast.error('Validation failed. Please review your entries.');
        } else {
            toast.error(err.message || 'An error occurred during submission.');
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <Head :title="isEditMode ? 'Edit Job Posting' : 'Post a New Job'" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Link & Title -->
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    href="/employer/jobs"
                    class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 hover:bg-gray-100 dark:bg-neutral-900 dark:border-neutral-800 dark:hover:bg-neutral-800 flex items-center justify-center text-gray-500 dark:text-neutral-400 transition"
                >
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ isEditMode ? 'Edit Job Posting' : 'Post a New Job' }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                        {{ isEditMode ? 'Modify your job post details.' : 'Create a new opportunity for candidates.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Full Page Loading Spinner during Metadata fetching -->
        <div v-if="isMetadataLoading" class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-24 text-center shadow-xs">
            <Loader2 class="w-10 h-10 text-indigo-500 animate-spin mx-auto mb-4" />
            <p class="text-gray-500 dark:text-neutral-400 text-sm">Initializing form data…</p>
        </div>

        <!-- Form Card -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-8">
            <!-- 1. GENERAL INFORMATION -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-xs space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-neutral-800 flex items-center gap-2">
                    <Briefcase class="w-5 h-5 text-indigo-500" />
                    <span>General Information</span>
                </h2>

                <!-- Job Title -->
                <div class="space-y-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Job Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="w-full h-11 px-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white dark:focus:ring-indigo-500/30"
                        :class="{ 'border-red-500 focus:ring-red-500/20': errors.title }"
                    />
                    <p v-if="errors.title" class="text-xs text-red-500 font-medium">{{ errors.title }}</p>
                </div>

                <!-- Location -->
                <div class="space-y-2">
                    <label for="location" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Location
                    </label>
                    <div class="relative">
                        <MapPin class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400" />
                        <input
                            id="location"
                            v-model="form.location"
                            type="text"
                            class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                        />
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-neutral-500">Leave blank if completely remote with no geographic requirement.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Work Type Selection Cards -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                            Work Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <div 
                                v-for="type in ['remote', 'onsite', 'hybrid']" 
                                :key="type"
                                @click="form.work_type = type as any"
                                class="flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer select-none text-center transition-all duration-200"
                                :class="form.work_type === type 
                                    ? 'border-indigo-600 bg-indigo-50/40 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 ring-2 ring-indigo-500/20' 
                                    : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:hover:bg-neutral-900/50 text-gray-600 dark:text-neutral-400'"
                            >
                                <span class="text-sm font-bold capitalize">{{ type === 'onsite' ? 'On-site' : type }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Experience Level Selection Cards -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                            Experience Level
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <div 
                                v-for="level in ['entry', 'mid', 'senior', 'lead']" 
                                :key="level"
                                @click="form.experience_level = level as any"
                                class="flex flex-col items-center justify-center py-3 px-1 rounded-xl border cursor-pointer select-none text-center transition-all duration-200"
                                :class="form.experience_level === level 
                                    ? 'border-indigo-600 bg-indigo-50/40 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 ring-2 ring-indigo-500/20' 
                                    : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:hover:bg-neutral-900/50 text-gray-600 dark:text-neutral-400'"
                            >
                                <span class="text-xs font-bold capitalize">{{ level }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Application Deadline -->
                    <div class="space-y-2">
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                            Application Deadline
                        </label>
                        <div class="relative">
                            <Calendar class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400" />
                            <input
                                id="deadline"
                                v-model="form.application_deadline"
                                type="date"
                                class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                                :class="{ 'border-red-500 focus:ring-red-500/20': errors.application_deadline }"
                            />
                        </div>
                        <p v-if="errors.application_deadline" class="text-xs text-red-500 font-medium">{{ errors.application_deadline }}</p>
                    </div>

                    <!-- Posting Status -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                            Posting Status <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <div 
                                v-for="status in ['active', 'draft', 'closed']" 
                                :key="status"
                                @click="form.status = status as any"
                                class="flex flex-col items-center justify-center py-3 rounded-xl border cursor-pointer select-none text-center transition-all duration-200"
                                :class="form.status === status 
                                    ? 'border-indigo-600 bg-indigo-50/40 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 ring-2 ring-indigo-500/20' 
                                    : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:hover:bg-neutral-900/50 text-gray-600 dark:text-neutral-400'"
                            >
                                <span class="text-xs font-bold capitalize">{{ status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TAXONOMY AND SALARY -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-xs space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-neutral-800 flex items-center gap-2">
                    <Layers class="w-5 h-5 text-indigo-500" />
                    <span>Classification & Salary</span>
                </h2>

                <!-- Salary Range -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Monthly Salary Range (USD)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <DollarSign class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" />
                            <input
                                v-model.number="form.salary_min"
                                type="number"
                                min="0"
                                class="w-full h-11 pl-8 pr-3 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                                :class="{ 'border-red-500': errors.salary_min }"
                            />
                            <span class="absolute right-3.5 top-3.5 text-xs text-gray-400 font-bold">MIN</span>
                        </div>
                        <div class="relative">
                            <DollarSign class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" />
                            <input
                                v-model.number="form.salary_max"
                                type="number"
                                min="0"
                                class="w-full h-11 pl-8 pr-3 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                                :class="{ 'border-red-500': errors.salary_max }"
                            />
                            <span class="absolute right-3.5 top-3.5 text-xs text-gray-400 font-bold">MAX</span>
                        </div>
                    </div>
                    <p v-if="errors.salary_min || errors.salary_max" class="text-xs text-red-500 font-medium">
                        {{ errors.salary_min || errors.salary_max }}
                    </p>
                </div>

                <!-- Categories Multi-selection Grid -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Job Categories
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="cat in categoriesList" 
                            :key="cat.id"
                            @click="toggleCategory(cat.id)"
                            class="px-4 py-2 rounded-xl text-xs font-semibold border cursor-pointer select-none transition-all flex items-center gap-1.5"
                            :class="form.category_ids.includes(cat.id) 
                                ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' 
                                : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-900'"
                        >
                            <Check v-if="form.category_ids.includes(cat.id)" class="w-3.5 h-3.5" />
                            <span>{{ cat.name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Technologies Multi-selection Grid -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 flex items-center gap-1.5">
                        <Cpu class="w-4 h-4 text-gray-400" />
                        <span>Required Technologies & Core Skills</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="tech in technologiesList" 
                            :key="tech.id"
                            @click="toggleTechnology(tech.id)"
                            class="px-4 py-2 rounded-xl text-xs font-semibold border cursor-pointer select-none transition-all flex items-center gap-1.5"
                            :class="form.technology_ids.includes(tech.id) 
                                ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' 
                                : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-900'"
                        >
                            <Check v-if="form.technology_ids.includes(tech.id)" class="w-3.5 h-3.5" />
                            <span>{{ tech.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. JOB SPECIFICS & TEXTS -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-xs space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-neutral-800 flex items-center gap-2">
                    <FileText class="w-5 h-5 text-indigo-500" />
                    <span>Job Specifications</span>
                </h2>

                <!-- Job Description -->
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Job Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        class="w-full p-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                        :class="{ 'border-red-500 focus:ring-red-500/20': errors.description }"
                    ></textarea>
                    <p v-if="errors.description" class="text-xs text-red-500 font-medium">{{ errors.description }}</p>
                </div>

                <!-- Responsibilities -->
                <div class="space-y-2">
                    <label for="responsibilities" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Key Responsibilities
                    </label>
                    <textarea
                        id="responsibilities"
                        v-model="form.responsibilities"
                        rows="4"
                        class="w-full p-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                    ></textarea>
                </div>

                <!-- Requirements -->
                <div class="space-y-2">
                    <label for="requirements" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Requirements & Qualifications
                    </label>
                    <textarea
                        id="requirements"
                        v-model="form.requirements"
                        rows="4"
                        class="w-full p-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                    ></textarea>
                </div>

                <!-- Benefits -->
                <div class="space-y-2">
                    <label for="benefits" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">
                        Benefits & Perks
                    </label>
                    <textarea
                        id="benefits"
                        v-model="form.benefits"
                        rows="3"
                        class="w-full p-3.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 transition dark:border-neutral-800 dark:bg-neutral-950 dark:text-white"
                    ></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4">
                <Link
                    href="/employer/jobs"
                    class="px-6 py-3 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800 font-semibold rounded-xl transition text-sm shadow-xs"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm shadow-sm flex items-center justify-center gap-2"
                >
                    <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
                    <span>{{ isEditMode ? 'Save Changes' : 'Post Job Listing' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
