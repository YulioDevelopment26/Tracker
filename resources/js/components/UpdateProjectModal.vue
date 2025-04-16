<script setup lang="ts">
import { ref, watch } from 'vue';
import { Dialog, DialogContent, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { useForm, router } from '@inertiajs/vue3';
import type { Project } from '@/types'

interface Developer {
  id: number;
  name: string;
  email: string;
}

const props = defineProps<{
  project: Project;
  developers: Developer[];
}>();

const open = ref(false);

const form = useForm({
  name: '',
  description: '',
  developers_ids: [] as number[],
  status: '',
});

watch(open, (isOpen) => {
  if (isOpen) {
    form.name = props.project.name;
    form.description = props.project.description;
    form.status = props.project.status;

    form.developers_ids = props.project.users.map((dev: Developer) => dev.id);
  }
});

const submit = () => {
  form.put(`/projects/${props.project.id}`, {
    onSuccess: () => {
      form.reset();
      open.value = false;
      router.reload();
    },
  });
};

const toggleDeveloper = (id: number) => {
  const index = form.developers_ids.indexOf(id);
  if (index === -1) {
    form.developers_ids.push(id);
  } else {
    form.developers_ids.splice(index, 1);
  }
};
</script>

<template>
  <div>
    <Button @click="open = true" class="border-black bg-black text-white hover:bg-gray-400 hover:border-white hover:text-black">
      Update Project
    </Button>

    <Dialog :open="open" @update:open="open = $event">
      <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
        <DialogTitle class="text-lg font-bold mb-4 text-gray-800">Update Project</DialogTitle>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <Input v-model="form.description" class="w-full border-gray-300 text-black bg-white" />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select v-model="form.status" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 bg-white text-black border capitalize">
              <option disabled :value="form.status">{{ props.project.status }}</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
              <option value="paused">Paused</option>
            </select>
          </div>

          <!-- Select developers -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Developers</label>
            <div class="border border-gray-300 rounded-md p-2 max-h-64 overflow-y-auto space-y-2">
              <div 
                v-for="developer in props.developers" 
                :key="developer.id"
                class="flex items-center space-x-2"
              >
                <input
                  type="checkbox"
                  :value="developer.id"
                  :checked="form.developers_ids.includes(developer.id)"
                  @change="toggleDeveloper(developer.id)"
                  class="text-blue-600 border-gray-300 rounded"
                />
                <span class="text-sm text-gray-800">{{ developer.name }} ({{ developer.email }})</span>
              </div>
              <div v-if="!props.developers?.length" class="text-gray-500 text-sm">No developers available</div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="open = false" class="bg-gray-200 text-gray-800 hover:bg-gray-300">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing" class="bg-blue-500 text-white hover:bg-blue-600">
              Update
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
