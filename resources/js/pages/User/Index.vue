<script setup lang="ts">
import { type BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import CardUser from '@/components/CardUser.vue'
import CreateUserModal from '@/components/CreateUserModal.vue'
import { Head } from '@inertiajs/vue3'

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'User',
    href: '/users',
  },
]

// User interface (singular)
interface User {
  id: number
  name: string
}

// Props: array of users
const props = defineProps<{
  users: User[]
}>()
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <!-- Title -->
      <div class="w-full flex justify-center pt-5">
        <h1 class="text-2xl font-bold mb-2">User List</h1>
      </div>

      <!-- Button to open modal -->
      <div class="w-full px-2 py-1 flex justify-end">
        <CreateUserModal />
      </div>

      <!-- User Cards -->
      <div class="w-full px-3 py-4">
        <div class="flex flex-wrap gap-4 justify-start">
          <template v-if="props.users.length">
            <CardUser
              v-for="user in props.users"
              :key="user.id"
              :user="user"
            />
          </template>
          <template v-else>
            <div class="text-center text-gray-500 w-full">
              Users not found
            </div>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
