<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user: User;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute the display name — handle first_name/last_name or name
const displayName = computed(() => {
    if (props.user?.name) return props.user.name as string;
    const first = (props.user as any)?.first_name ?? '';
    const last = (props.user as any)?.last_name ?? '';
    return `${first} ${last}`.trim() || props.user?.email || '';
});

const showAvatar = computed(
    () => props.user?.avatar && props.user.avatar !== '',
);
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :src="user.avatar!" :alt="displayName" />
        <AvatarFallback class="rounded-lg text-black dark:text-white font-semibold text-xs">
            {{ getInitials(displayName) || '?' }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ displayName }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
