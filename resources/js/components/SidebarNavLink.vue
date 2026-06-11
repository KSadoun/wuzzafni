<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { RouterLink } from 'vue-router';
import { computed } from 'vue';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

const props = defineProps<{
    item: NavItem;
}>();

const page = usePage();

const url = computed(() => toUrl(props.item.href));
const useVueRouter = computed(
    () => url.value.startsWith('/employer') && page.url.startsWith('/employer'),
);
</script>

<template>
    <RouterLink v-if="useVueRouter" :to="url" class="flex w-full items-center gap-2">
        <component :is="item.icon" v-if="item.icon" />
        <span>{{ item.title }}</span>
    </RouterLink>
    <Link v-else :href="item.href" class="flex w-full items-center gap-2">
        <component :is="item.icon" v-if="item.icon" />
        <span>{{ item.title }}</span>
    </Link>
</template>
