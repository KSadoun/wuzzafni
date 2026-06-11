<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import SidebarNavLink from '@/components/SidebarNavLink.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();

function isItemActive(item: NavItem): boolean {
    const path = toUrl(item.href);

    if (path.startsWith('/employer')) {
        return isCurrentOrParentUrl(item.href);
    }

    return isCurrentUrl(item.href);
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isItemActive(item)"
                    :tooltip="item.title"
                >
                    <SidebarNavLink :item="item" />
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
