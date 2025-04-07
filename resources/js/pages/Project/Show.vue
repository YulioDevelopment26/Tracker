<script setup lang="ts">
import { Project } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'

import Swal from 'sweetalert2';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Project',
    href: '/projects',
  },
]

const props = defineProps<{
  project: Project
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
                title: "Project eliminated"
            });
            return
        }
        Swal.fire({
            title: "Deleted",
            text: "Your project has been deleted",
            icon: "success"
        })
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
    <div class=" w-full flex justify-start m-2">
        <button @click="back" class=""> <- Back</button>
    </div>
    <div class="m-2 ">
        <div class="w-full flex justify-center">
            <h1>{{ props.project.name }}</h1>
        </div>
        <div class="w- full m-2">
            <span>{{ props.project.description }}</span>
        </div>
        <div class="bg-red-500 w-full flex justify-center">
            Sprint
        </div>
        
    </div>
    <div class="w-full flex justify-end p-5">
        <button class="bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 mr-1" :value="props.project.id" @click="deleteProject">Delete</button>
    </div>
      
  </AppLayout>
</template>
