<script setup lang="ts">
import { Project } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import CardUser from '@/components/CardUser.vue'
import CreateUserModal from '@/components/CreateUserModal.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'User',
    href: '/users',
  },
]

interface Users {
  id: number,
  name: string,
}

const props = defineProps<{
  users: Users
}>()

console.log(props.users)
</script>

<template>
  <Head title="Projects" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div>
        <!-- Title -->
        <div class="w-full flex justify-center pt-5">
          <h1 class="text-2xl font-bold mb-2">
            User List
          </h1>
        </div>

        <!-- Button create -->
        <div class="w-full px-2 py-1 flex justify-end">
          <!-- Modal for create -->
           <CreateUserModal>

           </CreateUserModal>
        </div>
      </div>

        <!-- Data List (card) -->
      <div class="w-full px-3 py-4">
        <div class="flex flex-wrap gap-4 justify-start">
          <template v-if="props.users.length">
            <CardUser
             v-for="user in props.users"
             :key="user.id"
             :user="user"
            />
          </template>
          <template v-else=>
            <div class="text-center text-gray-500 w-full">
              Users not found
            </div>
          </template>
        </div>
      </div>

    </div>
    <h1 class="text-white">{{ props.user }} </h1>
  </AppLayout>>
</template>
