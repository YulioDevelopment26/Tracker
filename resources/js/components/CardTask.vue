<script setup lang="ts">
import { computed, defineProps } from 'vue';
import UpdateTaskModal from './UpdateTaskModal.vue';

interface Task {
    id: number,
    name: string,
    description: string,
    estimated_start: string,
    estimated_finish: string,
    status: string,
    priority: string
}

const props = defineProps <{
    task: Task,
    permissions: string,
    project_id: number,
    sprint: any[]
}>()

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US')
}

const taskStatusIconPath = computed(() => {
  switch (props.task.status) {
    case 'to do':
      return 'M4 6h16M4 12h16M4 18h16';
    case 'in progress':
      return 'M12 4v1m0 14v1m8-8h1M3 12H2m15.07-6.93l.71.71M6.22 17.78l-.71.71m0-13.42l.71.71M17.78 17.78l-.71.71';
    case 'done':
      return 'M5 13l4 4L19 7';
    default:
      return 'M9 17v-2a4 4 0 014-4h6m-6 0V9a4 4 0 00-4-4H5a4 4 0 00-4 4v6a4 4 0 004 4h2';
  }
});

const taskPriorityStyles = computed(() => {
  switch (props.task.priority) {
    case 'low':
      return {
        classes: 'inline-flex items-center justify-center bg-yellow-200 text-yellow-900 border border-yellow-400 px-2 py-0.5 rounded-full text-xs font-semibold shadow-sm'
      };
    case 'medium':
      return {
        classes: 'inline-flex items-center justify-center bg-orange-200 text-orange-900 border border-orange-400 px-2 py-0.5 rounded-full text-xs font-semibold shadow-sm'
      };
    case 'high':
      return {
        classes: 'inline-flex items-center justify-center bg-red-200 text-red-900 border border-red-400 px-2 py-0.5 rounded-full text-xs font-semibold shadow-sm'
      };
    default:
      return {
        classes: 'inline-flex items-center justify-center bg-gray-200 text-gray-800 border border-gray-400 px-2 py-0.5 rounded-full text-xs font-semibold shadow-sm'
      };
  }
});


const taskStatusStyles = computed(() => {
  switch (props.task.status) {
    case 'to do':
      return {
        border: 'border-yellow-500',
        bg: 'from-white to-yellow-50',
        text: 'text-yellow-700',
        icon: 'text-yellow-500',
        button: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
      };
    case 'in progress':
      return {
        border: 'border-blue-500',
        bg: 'from-white to-blue-50',
        text: 'text-blue-700',
        icon: 'text-blue-500',
        button: 'bg-blue-100 text-blue-700 hover:bg-blue-200',
      };
    case 'done':
      return {
        border: 'border-green-500',
        bg: 'from-white to-green-50',
        text: 'text-green-700',
        icon: 'text-green-500',
        button: 'bg-green-100 text-green-700 hover:bg-green-200',
      };
    default:
      return {
        border: 'border-gray-400',
        bg: 'from-white to-gray-50',
        text: 'text-gray-700',
        icon: 'text-gray-400',
        button: 'bg-gray-100 text-gray-700 hover:bg-gray-200',
      };
  }
});
</script>
<template>
    <div :class="`w-full max-w-xs flex flex-col justify-between p-5 rounded-xl border-l-4 ${taskStatusStyles.border} bg-gradient-to-br ${taskStatusStyles.bg} shadow transition hover:shadow-md`">
        <!-- Task name -->
         <div class="flex justify-between">
             <h2 :class="`text-lg font-semibold ${taskStatusStyles.text} flex items-center gap-2 break-words`">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="taskStatusStyles.icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="taskStatusIconPath" />
                 </svg>
                 {{ props.task.id }}. 
                 {{ props.task.name.length > 12 ? props.task.name.slice(0, 12) + '...' : props.task.name }}  
             </h2>
             <span :class="`${ taskPriorityStyles.classes }`">
                {{ props.task.priority.toUpperCase() }}
             </span>
         </div>

        <!-- Task description -->
        <p class="text-sm text-gray-700 mt-2 italic break-words">
            {{ props.task.description.length > 30 ? props.task.description.slice(1, 30) + '...' : props.task.description }}
        </p>

        <!-- Date -->
        <div class="mt-auto">
            <h3 :class="`text-lg font-semibold ${taskStatusStyles.text} flex items-center gap-2 break-words`">Estimated date</h3>
            <div class="text-xs text-gray-600 flex items-center gap-2 mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ formatDate(props.task.estimated_start) }} → {{ formatDate(props.task.estimated_finish) }}
            </div>
            <div class="flex justify-end mt-2">
                <UpdateTaskModal 
                 :style="taskStatusStyles.button"
                 :task_id="props.task.id"
                 :permissions="props.permissions"
                 :project_id="props.project_id"
                 :sprint="props.sprint"
                />
            </div>
        </div>
    </div>
</template>