<script setup lang="ts">
import { ref } from 'vue';
import {
  DialogRoot,
  DialogTrigger,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogTitle,
  DialogDescription,
  DialogClose,
} from 'reka-ui';
import { X, UploadCloud } from 'lucide-vue-next';

const props = defineProps<{
    jobId: number | string;
}>();

const emit = defineEmits<{
    (e: 'apply', formData: FormData | { use_existing_resume: boolean; cover_letter?: string; phone?: string; email?: string }): void;
}>();

const isOpen = ref(false);
const useProfileResume = ref(false);
const selectedFile = ref<File | null>(null);
const email = ref('');
const phone = ref('');
const coverLetter = ref('');

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
        useProfileResume.value = false;
    }
}

function submitApplication() {
    if (selectedFile.value) {
        // Send as FormData
        const formData = new FormData();
        formData.append('resume', selectedFile.value);
        formData.append('use_existing_resume', '0');
        if (email.value) formData.append('email', email.value);
        if (phone.value) formData.append('phone', phone.value);
        if (coverLetter.value) formData.append('cover_letter', coverLetter.value);
        
        emit('apply', formData);
    } else {
        // Send as JSON
        emit('apply', {
            use_existing_resume: useProfileResume.value,
            email: email.value || undefined,
            phone: phone.value || undefined,
            cover_letter: coverLetter.value || undefined
        });
    }
    
    isOpen.value = false;
    // reset
    selectedFile.value = null;
    coverLetter.value = '';
    useProfileResume.value = false;
}
</script>

<template>
  <DialogRoot v-model:open="isOpen">
    <DialogTrigger asChild>
      <slot name="trigger">
          <button class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
              Apply Now
          </button>
      </slot>
    </DialogTrigger>
    <DialogPortal>
      <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" />
      <DialogContent class="fixed left-1/2 top-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl p-6 sm:p-8 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%]">
        
        <div class="flex items-center justify-between mb-6">
            <DialogTitle class="text-2xl font-bold text-gray-900">Submit Application</DialogTitle>
            <DialogClose class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors">
                <X class="w-5 h-5" />
            </DialogClose>
        </div>
        
        <DialogDescription class="text-gray-600 mb-6">
            Please review and complete your application details below.
        </DialogDescription>

        <form @submit.prevent="submitApplication" class="space-y-5">
            <!-- Resume Selection -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Resume <span class="text-red-500">*</span></label>
                
                <div class="flex items-center gap-3 p-3 border rounded-lg bg-gray-50 border-gray-200 cursor-pointer" @click="useProfileResume = !useProfileResume; if(useProfileResume) selectedFile = null">
                    <input type="checkbox" :checked="useProfileResume" class="w-4 h-4 text-blue-600 rounded" />
                    <span class="text-sm text-gray-800">Use my existing profile resume</span>
                </div>

                <div class="border-2 border-dashed rounded-xl p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative" :class="[selectedFile ? 'border-blue-400 bg-blue-50' : 'border-gray-300', useProfileResume ? 'opacity-50' : '']">
                    <input type="file" @change="handleFileChange" accept=".pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" :disabled="useProfileResume" />
                    <UploadCloud class="w-8 h-8 mx-auto text-gray-400 mb-2" />
                    <p class="text-sm font-medium text-gray-900" v-if="selectedFile">{{ selectedFile.name }}</p>
                    <p class="text-sm text-gray-600" v-else>Upload a new resume (PDF, DOC)</p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="email" v-model="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" v-model="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
            </div>

            <!-- Cover Letter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Letter <span class="text-gray-400 font-normal">(Optional)</span></label>
                <textarea v-model="coverLetter" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" placeholder="Introduce yourself..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <DialogClose asChild>
                    <button type="button" class="px-5 py-2.5 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                </DialogClose>
                <button type="submit" :disabled="!useProfileResume && !selectedFile" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Submit Application
                </button>
            </div>
        </form>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
