<script setup>
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps({
  jobs: Array,
  currentStatus: String
});

const updateStatus = (id, status) => {
  router.patch(route('admin.jobs.updateStatus', id), { status });
};

const switchTab = (status) => {
  router.get(route('admin.jobs.index'), { status });
};
</script>

<template>
  <div class="p-8 space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold">Job Post Approvals Desk</h1>
        <p class="text-sm text-muted-foreground">Evaluate, approve, or decline submittals here.</p>
      </div>
      
      <div class="flex space-x-2 bg-muted p-1 rounded-md">
        <Button size="sm" :variant="currentStatus === 'pending' ? 'default' : 'ghost'" @click="switchTab('pending')">Pending</Button>
        <Button size="sm" :variant="currentStatus === 'approved' ? 'default' : 'ghost'" @click="switchTab('approved')">Approved</Button>
        <Button size="sm" :variant="currentStatus === 'rejected' ? 'default' : 'ghost'" @click="switchTab('rejected')">Rejected</Button>
      </div>
    </div>

    <div v-if="jobs.length === 0" class="text-center py-12 text-muted-foreground border rounded-xl border-dashed">
      No job postings found under "{{ currentStatus }}".
    </div>

    <div v-else class="space-y-4">
      <Card v-for="job in jobs" :key="job.id" class="w-full">
        <CardHeader>
          <div class="flex justify-between items-start">
            <div>
              <CardTitle class="text-xl">{{ job.title }}</CardTitle>
              <p class="text-sm text-muted-foreground mt-1">
                Company profile ID: {{ job.employer_profile_id }} | Range: ${{ job.salary_min }} - ${{ job.salary_max }}
              </p>
            </div>
            <Badge :variant="job.status === 'approved' ? 'success' : job.status === 'rejected' ? 'destructive' : 'warning'">
              {{ job.status }}
            </Badge>
          </div>
        </CardHeader>
        
        <CardContent class="space-y-4">
          <div>
            <h4 class="text-sm font-semibold">Description</h4>
            <p class="text-sm text-muted-foreground">{{ job.description }}</p>
          </div>

          <div v-if="job.requirements">
            <h4 class="text-sm font-semibold">Requirements</h4>
            <p class="text-sm text-muted-foreground">{{ job.requirements }}</p>
          </div>

          <div class="flex flex-wrap gap-2 pt-2">
            <Badge variant="outline">{{ job.work_type }}</Badge>
            <Badge variant="outline">{{ job.location }}</Badge>
            <Badge variant="outline">{{ job.experience_level }}</Badge>
          </div>

          <div v-if="job.status === 'pending'" class="flex gap-2 pt-4 border-t">
            <Button size="sm" class="bg-green-600 hover:bg-green-700" @click="updateStatus(job.id, 'approved')">Approve Post</Button>
            <Button size="sm" variant="destructive" @click="updateStatus(job.id, 'rejected')">Reject Post</Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>