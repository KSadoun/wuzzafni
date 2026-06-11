<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <!-- Wuzzafni briefcase logo icon -->
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <!-- Briefcase body -->
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2" fill="currentColor" stroke="none" opacity="0.85"/>
        <!-- Handle -->
        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="2" fill="none"/>
        <!-- Center divider line -->
        <line x1="12" y1="12" x2="12" y2="18" stroke="white" stroke-width="1.5" opacity="0.6"/>
        <!-- Horizontal center line -->
        <line x1="2" y1="13" x2="22" y2="13" stroke="white" stroke-width="1.5" opacity="0.4"/>
    </svg>
</template>
