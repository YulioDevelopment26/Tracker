<script setup lang="ts">
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
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { onMounted, ref } from 'vue';


const allNavItems: (NavItem & { requiresAdmin?: boolean })[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
    icon: LayoutGrid,
  },
  {
    title: 'Project',
    href: '/projects',
    icon: Folder,
  },
  {
    title: 'User',
    href: '/users',
    icon: Users,
    requiresAdmin: true,
  },
];

const userRole = ref<string | null>(null);

const roleUser = async () => {
  const response = await fetch('/api/user/role');
  const data = await response.json();
  userRole.value = data.role;
};

const mainNavItems = ref<NavItem[]>([]);

onMounted(async () => {
  await roleUser();

  mainNavItems.value = allNavItems.filter(item => {
    if (userRole.value === 'admin') return true;
    return !item.requiresAdmin;
  });
});
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
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
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>
