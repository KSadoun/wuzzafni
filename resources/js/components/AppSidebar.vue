<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { RouterLink } from 'vue-router';
import { computed } from 'vue';
import { BookOpen, FolderGit2, LayoutGrid, Briefcase, FileText } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useDashboardUrl } from '@/composables/useDashboardUrl';
import { toUrl } from '@/lib/utils';
import candidate from '@/routes/candidate';
import jobs from '@/routes/jobs';
import type { NavItem } from '@/types';

const page = usePage();
const userRole = computed(() => (page.props.auth as { user?: { role?: string } })?.user?.role);
const dashboardUrl = useDashboardUrl();

const logoNavItem = computed<NavItem>(() => ({
    title: 'Home',
    href: dashboardUrl.value,
}));

const useLogoRouter = computed(
    () => toUrl(logoNavItem.value.href).startsWith('/employer') && page.url.startsWith('/employer'),
);

const mainNavItems = computed<NavItem[]>(() => {
    if (userRole.value === 'candidate') {
        return [
            {
                title: 'My Applications',
                href: candidate.applications(),
                icon: FileText,
            },
            {
                title: 'Browse Jobs',
                href: jobs.index(),
                icon: Briefcase,
            },
        ];
    }

    if (userRole.value === 'employer') {
        return [
            {
                title: 'My Jobs',
                href: '/employer/jobs',
                icon: Briefcase,
            },
        ];
    }

    return [
        {
            title: 'Dashboard',
            href: dashboardUrl.value,
            icon: LayoutGrid,
        },
        {
            title: 'Browse Jobs',
            href: jobs.index(),
            icon: Briefcase,
        },
    ];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <RouterLink v-if="useLogoRouter" :to="toUrl(logoNavItem.href)" class="flex w-full items-center">
                            <AppLogo />
                        </RouterLink>
                        <Link v-else :href="logoNavItem.href" class="flex w-full items-center">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
