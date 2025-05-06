<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

interface Task {
  id: number;
  name: string;
  status: string;
  due_date: string;
  updated_at: string;
  estimated_hours: number;
  actual_hours: number;
  user: {
    name: string;
    hour_value: number;
  };
}

interface Sprint {
  id: number;
  name: string;
  tasks: Task[];
  start_date: string;
  end_date: string;
}

interface Project {
  id: number;
  name: string;
  start_date: string;
  end_date: string;
  sprints: Sprint[];
}

const props = defineProps<{
  projects: Project[];
  permissions: string;
}>();

const activeSprintId = ref<number | null>(null);

const openModal = (sprintId: number) => {
  activeSprintId.value = sprintId;
};

const closeModal = () => {
  activeSprintId.value = null;
};

const getPaymentsBySprint = (sprint: Sprint) => {
  const result: Record<string, number> = {};

  sprint.tasks.forEach(task => {
    if (task.status === 'done' && task.user) {
      const name = task.user.name || 'Unassigned';
      const pay = (task.user.hour_value || 0) * (task.actual_hours || 0);
      result[name] = (result[name] || 0) + pay;
    }
  });

  return result;
};
console.log(props)
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs" v-if="props.permissions === 'admin'">
    <h1 class="text-2xl font-semibold text-center my-6">Weekly Information</h1>

    <div class="w-full px-6">
      <template v-if="props.projects && props.projects.length">
        <div v-for="project in props.projects" :key="project.id" class="mb-6">
          <details class="group bg-background border border-foreground/20 rounded-lg shadow p-4">
            <summary class="cursor-pointer text-lg font-bold text-blue-600 group-open:text-blue-800">
              {{ project.name }} <span class="text-sm text-gray-500">– Project</span>
            </summary>

            <!-- Sprints -->
            <div class="mt-3 space-y-3 ml-4">
              <div v-for="sprint in project.sprints" :key="sprint.id">
                <details class="group bg-gray-50 border border-gray-200 rounded-md p-3">
                  <summary class="cursor-pointer text-base font-medium text-green-600 group-open:text-green-800 flex justify-between items-center">
                    <span>{{ sprint.name }} <span class="text-sm text-gray-500">– Sprint / {{ sprint.start_date.split('T')[0] }} to {{ sprint.end_date.split('T')[0] }}</span></span>
                    <button
                      class="text-xs text-blue-600 hover:underline"
                      @click.stop="openModal(sprint.id)"
                    >
                      See payments
                    </button>
                  </summary>

                  <!-- Tasks -->
                  <div class="mt-4 ml-4 space-y-6">
                    <!--  DONE TASKS -->
                    <div v-if="sprint.tasks.some(task => task.status === 'done')">
                      <h4 class="text-sm font-semibold text-green-600 mb-2">Done</h4>
                      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                          v-for="task in sprint.tasks.filter(task => task.status === 'done')"
                          :key="task.id"
                          class="bg-green-50 border border-green-200 rounded-lg shadow p-4 hover:shadow-md transition"
                        >
                          <h3 class="text-sm font-semibold text-gray-800 mb-1">
                            {{ task.name }}
                          </h3>
                          <p class="text-xs text-gray-500">
                            <span class="font-bold text-gray-600">Assigned to:</span>
                            {{ task.user.name ?? 'Unassigned' }}
                          </p>
                          <p class="text-xs text-gray-500">
                            <span class=" text-gray-600 font-bold">Due date:</span>
                            {{ task.updated_at.split('T')[0] }}
                          </p>
                          <p class="text-xs text-gray-500">
                            <span class="font-bold text-gray-600">Estimated hours:</span>
                            {{ task.estimated_hours ?? 'Unassigned' }}
                          </p>
                          <p class="text-xs text-gray-500">
                            <span class="font-bold text-gray-600">Real hours:</span>
                            {{ task.actual_hours ?? 0 }}
                          </p>
                          <p class="text-xs text-gray-500">
                            <span class="font-bold text-gray-600">Pay:</span>
                            {{ task.user.hour_value * (task.actual_hours ?? 0) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </details>

                <div
                  v-if="activeSprintId === sprint.id"
                  class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
                >
                  <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4 text-black">Sprint payments: {{ sprint.name }}</h2>
                    <ul class="divide-y divide-gray-200">
                      <li
                        v-for="(pay, user) in getPaymentsBySprint(sprint)"
                        :key="user"
                        class="py-2 flex justify-between"
                      >
                        <span class="text-sm text-gray-700">{{ user }}</span>
                        <span class="text-sm font-semibold text-green-700">${{ pay }}</span>
                      </li>
                    </ul>
                    <div class="mt-6 text-right">
                      <button
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                        @click="closeModal"
                      >
                        Close
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </details>
        </div>
      </template>

      <template v-else>
        <div class="w-full text-center py-10 text-gray-500 italic">
          No projects available.
        </div>
      </template>
    </div>
  </AppLayout>
  <AppLayout v-if="props.permissions === 'developer'">
    <h1 class="text-2xl font-bold mb-6 text-center">My Tasks</h1>

    <div class="w-full px-6 space-y-6">
      <template v-if="props.projects.length">
        <div
          v-for="project in props.projects"
          :key="project.id"
          class="border border-gray-200 bg-white rounded-lg shadow p-4"
        >
          <details class="group">
            <summary class="cursor-pointer text-lg font-bold text-blue-700 group-open:text-blue-900 flex justify-between items-center">
              {{ project.name }}
              <Link
                :href="`/projects/${project.id}/tasks`"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded transition"
              >
                Go to Tasks
              </Link>
            </summary>

            <div class="mt-4 space-y-4 ml-4">
              <div
                v-for="sprint in project.sprints"
                :key="sprint.id"
              >
                <details
                  v-if="sprint.tasks.length"
                  class="group bg-gray-50 border border-gray-200 rounded-md p-3"
                >
                  <summary class="cursor-pointer text-base font-medium text-green-600 group-open:text-green-800">
                    {{ sprint.name }} 
                    <span class="text-sm text-gray-500">– {{ sprint.start_date.split('T')[0] }} to {{ sprint.end_date.split('T')[0] }}</span>
                  </summary>

                  <div class="mt-4 ml-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                      v-for="task in sprint.tasks"
                      :key="task.id"
                      class="bg-white border border-gray-200 rounded-lg shadow p-4 hover:shadow-md transition"
                    >
                      <h3 class="text-sm font-semibold text-gray-800 mb-1">
                        {{ task.name }}
                      </h3>
                      <p class="text-xs text-gray-500">
                        <span class="font-bold text-gray-600">Status:</span> {{ task.status }}
                      </p>
                      <p class="text-xs text-gray-500">
                        <span class="font-bold text-gray-600">Due date:</span> {{ task.updated_at.split('T')[0] }}
                      </p>
                      <p class="text-xs text-gray-500">
                        <span class="font-bold text-gray-600">Estimated hours:</span> {{ task.estimated_hours ?? 'N/A' }}
                      </p>
                    </div>
                  </div>
                </details>
              </div>
            </div>
          </details>
        </div>
      </template>

      <template v-else>
        <div class="text-center text-gray-500 italic">
          No tasks assigned to you.
        </div>
      </template>
    </div>

  </AppLayout>
</template>
