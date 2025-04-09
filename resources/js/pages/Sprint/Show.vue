<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import Swal from 'sweetalert2'
import UpdateSprintModal from '@/components/UpdateSprintModal.vue'


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Sprint',
    href: '/sprints',
  },
]

interface Sprint {
    id: number,
    goal: string,
    name: string,
    start_date: string,
    end_date: string,
}

const props = defineProps<{
    sprint: Sprint,
    tasks: any[]
    permissions: string
    project_id: number
}>()


const back = () => {
    window.location.href = `/projects/${props.project_id}`
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US');
}

const deleteSprint = async () => {
    const result  = await Swal.fire({
        title: "Are you sure?",
        text: `You want to delete ${props.sprint.name}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancel",
        confirmButtonText: "Yes",
    })
    if (!result.isConfirmed) return

    try {
        const response = await fetch(`/projects/${props.project_id}/sprints/${props.sprint.id}`, {
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
            text: "Your sprint has been deleted",
            icon: "success"
        }).then(() => {
            window.location.href = `/projects/${props.project_id}`;
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
console.log(props)

</script>

<template>
    <Head title="Sprints" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Sprint title -->
         <div class="w-full flex justify-center pt-6">
            <h1 class="text-3xl fond-bold text-gray-500">{{ props.sprint.name }}</h1>
         </div>

        <!-- Buttons back and update -->
         <div class="w-full flex justify-between items-center mt-4 px-5">
            <button
                @click="back"
                class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium"
            >
            <span class="text-lg">&#8592;</span> Back
            </button>
            <UpdateSprintModal
                v-if="props.permissions === 'admin'"
                :sprint="props.sprint"
                :project_id="props.project_id"
            />
         </div>

         <!-- Sprint content -->
          <div class="m-4 space-y-6 mb-10">
            <!-- Goal and date -->
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Goal</h2>
                <p class="text-gray-700">{{ props.sprint.goal }}</p>
                <span class="text-gray-600 ml-10 italic">{{ formatDate(props.sprint.start_date) }} -> {{ formatDate(props.sprint.end_date) }}</span>
            </div>
          </div>

          <!-- Tasks Placeholder -->
           <h1 class="text-center text-gray-400 w-full font-medium text-2xl">Tasks</h1>
           <template v-if="props.tasks">

           </template>
           <template v-else>
            <div class="bg-red-50 p-4 rounded shadow-sm border border-red-200 flex items-center justify-between m-4">
                <span class="text-sm text-red-500">No tasks have been created yet</span>
            </div>
           </template>

          <!-- Delete button -->
           <hr>
           <div class="w-full flex justify-start pb-6 px-5 pt-4">
            <button
                class="bg-red-100 text-red-600 px-4 py-2 rounded hover:bg-red-200 font-semibold"
                :value="props.sprint.id"
                @click="deleteSprint"
                v-if="props.permissions === 'admin' && !props.tasks "
            >
            Delete
            </button>
           </div>

    </AppLayout>
</template>