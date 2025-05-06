<script setup lang="ts">
import { defineProps } from 'vue'
import { Button } from '@/components/ui/button';

type ProjectStatus = 'active' | 'inactive' | 'completed' | 'cancelled' | 'paused'

interface Project {
  id: number
  name: string
  description: string
  developer_user_id: number | null
  create_by: number | string | null 
  created_at: string
  updated_at: string
  deleted_at: string | null
  status: ProjectStatus
}

const props = defineProps<{
  project: Project
}>()

const formatDate = (dateStr: string): string => {
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateStr).toLocaleDateString('en-US', options)
}

function getStatusClass(status: ProjectStatus): string {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'inactive':
      return 'bg-gray-100 text-gray-600'
    case 'completed':
      return 'bg-blue-100 text-blue-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    case 'paused':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-600'
  }
}

const showProject = () => {
  window.location.href = `/projects/${props.project.id}`
}

</script>

<template>
  <div class="max-w-xs mx-auto mt-6">
    <div class="bg-background rounded-2xl shadow-md p-6 border border-foreground/20">
      <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-bold ">
          {{ props.project.name.length > 12
          ? props.project.name.slice(0, 12) + '...'
          : props.project.name }}
        </h2>
        <span
          class="px-3 py-1 text-sm rounded-full capitalize"
          :class="getStatusClass(props.project.status)"
        >
          {{ props.project.status }}
        </span>
      </div>
      <p class="text-gray-600 mb-2">
        {{ props.project.description.length > 30
          ? props.project.description.slice(0, 30) + '...'
          : props.project.description }}
      </p>

      <div class="text-sm text-gray-500">
        <p><span class="font-medium">ID:</span> {{ props.project.id }}</p>
        <p><span class="font-medium">Created by:</span> {{ props.project.create_by }}</p>
        <p><span class="font-medium">Created:</span> {{ formatDate(props.project.created_at) }}</p>
      </div>
      <div class="w-full flex justify-end mt-2">
        <Button :value="props.project.id" @click="showProject">Show</Button>
      </div>
    </div>
  </div>
</template>
