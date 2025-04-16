<script setup lang="ts">
import { ref, defineProps } from 'vue';
import { Dialog, DialogContent, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { useForm, router } from '@inertiajs/vue3';

interface Developer {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    developers?: Developer[];
}>();

const open = ref(false);

const form = useForm({
  name: '',
  description: '',
  developers_ids: [] as number[],
});

const toggleDeveloper = (id: number) => {
  const index = form.developers_ids.indexOf(id);
  if (index === -1) {
    form.developers_ids.push(id);
  } else {
    form.developers_ids.splice(index, 1);
  }
};

const submit = () => {
  form.post('/projects', {
    onSuccess: () => {
      form.reset();
      open.value = false;
      router.reload();
    },
  });
};
</script>

<template>
  <div>
    <Button @click="open = true" class="border-black bg-black text-white hover:bg-gray-400 hover:border-white hover:text-black">
      New Project
    </Button>

    <Dialog :open="open" @update:open="open = $event">
      <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
        <DialogTitle class="text-lg font-bold mb-4 text-gray-800">New Project</DialogTitle>

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

          <!-- Developers Checkboxes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Developers</label>
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
              Save
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
