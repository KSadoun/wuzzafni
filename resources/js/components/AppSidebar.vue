<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, FolderGit2, LayoutGrid, Briefcase, FileText, PlusCircle } from 'lucide-vue-next';
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
import { dashboard } from '@/routes';
import candidate from '@/routes/candidate';
import jobs from '@/routes/jobs';
import type { NavItem } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth.user);

const homeHref = computed(() => {
    return user.value?.role === 'employer' ? '/employer/jobs' : dashboard();
});

const mainNavItems = computed<NavItem[]>(() => {
    const role = user.value?.role;
    const items: NavItem[] = [];

    if (role !== 'employer') {
        items.push({
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        });
    }

    items.push({
        title: 'Jobs Board',
        href: jobs.index(),
        icon: Briefcase,
    });

    if (role === 'employer') {
        items.push(
            {
                title: 'My Job Posts',
                href: '/employer/jobs',
                icon: FileText,
            },
            {
                title: 'Post a Job',
                href: '/employer/jobs/create',
                icon: PlusCircle,
            }
        );
    } else {
        items.push({
            title: 'My Applications',
            href: candidate.applications(),
            icon: FileText,
        });
    }

    return items;
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
                        <Link :href="homeHref">
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
