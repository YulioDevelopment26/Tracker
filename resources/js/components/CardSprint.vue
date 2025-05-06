<script setup lang="ts">
import { defineProps } from 'vue';

interface Sprint {
    id: number,
    name: string,
    goal: string,
    start_date: string,
    end_date: string
}

const props = defineProps <{
    sprint: Sprint,
    permissions: string,
    project_id: number
}>()

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US');
}

const showSprint = () => {
    window.location.href = `/projects/${props.project_id}/sprints/${props.sprint.id}`
}
</script>
<template>
    <div :class="['w-full max-w-xs  flex flex-col justify-between p-5 rounded-xl border-l-4 bg-gradient-to-br from-background to-primary/10 border border- shadow transition hover:shadow-md', new Date(props.sprint.end_date) < new Date() ? 'border-gray-500' : 'border-blue-500']">
    
        <!-- Sprint name -->
        <h2 :class="['text-lg font-semibold flex items-center gap-2 break-words', new Date(props.sprint.end_date) < new Date() ? 'text-gray-500' : 'text-blue-600']">
            <svg xmlns="http://www.w3.org/2000/svg" :class="['h-5 w-5', new Date(props.sprint.end_date) < new Date() ? 'text-gray-500' : 'text-blue-500']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h6m-6 0V9a4 4 0 00-4-4H5a4 4 0 00-4 4v6a4 4 0 004 4h2" />
            </svg>
            {{ props.sprint.id }}.
            {{ props.sprint.name.length > 25
            ? props.sprint.name.slice(0, 25) + '...'
            : props.sprint.name }}
        </h2>

        <!-- Sprint goal -->
        <p class="text-sm  mt-2 italic break-words">
            {{ props.sprint.goal.length > 30 
            ? props.sprint.goal.slice(1, 30) + '...'
            : props.sprint.goal }}
        </p>

        <!-- Date and view more -->
        <div class="mt-auto">
            <div class="text-xs text-gray-400 flex items-center gap-2 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(props.sprint.start_date) }} → {{ formatDate(props.sprint.end_date) }}
            </div>
            <div class="flex justify-end mt-2">
                <button :class="['px-2 py-1 rounded', new Date(props.sprint.end_date) < new Date() ? 'text-gray-400 hover:bg-gray-200 bg-gray-200' : 'text-blue-600 hover:bg-blue-200 bg-blue-100']" type="button" @click="showSprint">
                    View more
                </button>
            </div>
        </div>
    </div>

</template>