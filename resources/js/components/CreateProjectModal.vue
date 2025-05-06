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
    <Button @click="open = true" >
      New Project
    </Button>

    <Dialog :open="open" @update:open="open = $event">
      <DialogContent class="max-w-md p-6 bg-background rounded-lg shadow-lg">
        <DialogTitle class="text-lg font-bold mb-4">New Project</DialogTitle>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium">Name</label>
            <Input v-model="form.name" class="w-full border-gray-100/30  bg-background" />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium">Description</label>
            <Input v-model="form.description" class="w-full border-gray-100/30  bg-background" />
          </div>

          <!-- Developers Checkboxes -->
          <div>
            <label class="block text-sm font-medium mb-1">Developers</label>
            <div class="border border-gray-100/30 rounded-md p-2 max-h-64 overflow-y-auto space-y-2">
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
                  class="text-blue-600 border-gray-100/30 rounded"
                />
                <span class="text-sm ">{{ developer.name }} ({{ developer.email }})</span>
              </div>
              <div v-if="!props.developers?.length" class="text-gray-500 text-sm">No developers available</div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="open = false" class=" ">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing" >
              Save
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
