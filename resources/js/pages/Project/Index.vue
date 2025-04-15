<script setup lang="ts">
import { Project } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import ProjectCreateModal from '@/components/CreateProjectModal.vue'
import CardProject from '@/components/CardProject.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: '/projects',
  },
]

const props = defineProps<{
  projects: Project[],
  permissions: string,
  developers: any[]
}>()

</script>

<template>
  <Head title="Projects" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div>
        <!-- Title -->
        <div class="w-full flex justify-center pt-5">
          <h1 class="text-2xl font-bold mb-2">Project List</h1>
        </div>

        <!-- Create button -->
        <div class="w-full px-2 py-1 flex justify-end" v-if="props.permissions === 'admin'">
          <ProjectCreateModal :developers="props.developers" />
        </div>
      </div>
      <div class="w-full px-3 py-4">
        <div class="flex flex-wrap gap-4 justify-start">
          <template v-if="props.projects.length">
            <CardProject
              v-for="project in props.projects"
              :key="project.id"
              :project="project"
              :permissions="props.permissions"
              class="w-full sm:w-[48%] lg:w-[31%]" 
              />
          </template>
          <template v-else>
            <h1 class="text-center text-gray-500 w-full">Projects not found</h1>
          </template>
        </div>
      </div>

      

      <!-- Projects -->
      
    </div>
  </AppLayout>
</template>
