<script setup lang="ts">
import { Project } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import UpdateProjectModal from '@/components/UpdateProjectModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Project',
    href: '/projects',
  },
]

const props = defineProps<{
  project: Project,
  developers: any[],
  permissions: string
}>()

const back = () => {
    window.history.back()
}

const deleteProject = async () => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: `You want to delete ${props.project.name}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancel",
        confirmButtonText: "Yes",
    })

    if (!result.isConfirmed) return  

    try{
        const response = await fetch(`/projects/${props.project.id}`, {
            method: 'DELETE',
            headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
            }
        })

        if (!response.ok) {
            const data = await response.json()
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: data.message
            });
            return
        }
        Swal.fire({
            title: "Deleted",
            text: "Your project has been deleted",
            icon: "success"
            
        }).then(() => {
            window.location.href = '/projects';
        });
        

        } catch (error){
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer
                    toast.onmouseleave = Swal.resumeTimer
                }
            })

            Toast.fire({
                icon: 'error',
                title: 'Error while trying to delete'
            })        
                    
        }
}
</script>

<template>
    <Head title="Projects" />
  
    <AppLayout :breadcrumbs="breadcrumbs">
      <!-- Project title -->
      <div class="w-full flex justify-center pt-6">
        <h1 class="text-3xl font-bold text-gray-500">{{ props.project.name }}</h1>
      </div>
  
      <!-- Top bar: back and update buttons -->
      <div class="w-full flex justify-between items-center mt-4 px-5">
        <button
          @click="back"
          class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium"
        >
          <span class="text-lg">&#8592;</span> Back
        </button>
        <UpdateProjectModal
          v-if="props.permissions === 'admin'"
          :project="props.project"
          :developers="props.developers"
        />
      </div>
  
      <!-- Project content -->
      <div class="m-4 space-y-6">
        <!-- Description -->
        <div class="bg-gray-100 p-4 rounded shadow-sm">
          <h2 class="text-lg font-semibold text-gray-700 mb-2">Description</h2>
          <p class="text-gray-600">{{ props.project.description }}</p>
        </div>
  
        <!-- Developer list -->
        <div class="bg-white p-4 rounded shadow-sm border">
          <h2 class="text-lg font-semibold text-gray-700 mb-2">Developers in the project</h2>
          <ul class="list-disc list-inside text-gray-700 space-y-1">
            <template v-if="props.developers.length">
                <li v-for="developer in props.developers" :key="developer.id">
                {{ developer.name }} — <span class="text-sm text-gray-500">{{ developer.email }}</span>
                </li>
            </template>
            <li v-else class="text-sm text-gray-500 list-none">
                No developer partners associated with this project
            </li>
          </ul>
        </div>
  
        <!-- Sprint placeholder -->
        <div class="bg-red-100 p-4 text-center text-red-600 font-medium rounded shadow-sm">
          Sprint
        </div>
      </div>
  
      <!-- Delete button -->
      <div class="w-full flex justify-end pb-6 px-5">
        <button
          class="bg-red-100 text-red-600 px-4 py-2 rounded hover:bg-red-200 font-semibold"
          :value="props.project.id"
          @click="deleteProject"
          v-if="props.permissions === 'admin'"
        >
          Delete
        </button>
      </div>
    </AppLayout>
  </template>
  
