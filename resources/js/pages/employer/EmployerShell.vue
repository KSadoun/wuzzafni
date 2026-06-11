<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { RouterView } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Employer" />
    <RouterView />
</template>
