<script setup>
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

defineProps({
  comments: Array
});

const removeComment = (id) => {
  if (confirm('Are you absolutely sure you want to remove this comment permanently?')) {
    router.delete(route('admin.comments.destroy', id));
  }
};
</script>

<template>
  <div class="p-8 space-y-6">
    <div>
      <h1 class="text-2xl font-bold">Comment Moderation Center</h1>
      <p class="text-sm text-muted-foreground">Monitor discussion segments and remove offensive user interactions.</p>
    </div>

    <div v-if="comments.length === 0" class="text-muted-foreground text-center py-8">
      No interactions posted yet.
    </div>

    <div v-else class="space-y-3">
      <Card v-for="comment in comments" :key="comment.id">
        <CardContent class="p-4 flex justify-between items-center">
          <div class="space-y-1">
            <p class="text-sm font-medium text-foreground">"{{ comment.content }}"</p>
            <p class="text-xs text-muted-foreground">
              Posted by: <span class="font-semibold">{{ comment.user?.name || 'User ' + comment.user_id }}</span> 
              on Job Advert: <span class="italic">"{{ comment.job?.title || 'Unknown Job' }}"</span>
            </p>
          </div>
          <Button size="sm" variant="destructive" @click="removeComment(comment.id)">
            Remove
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>