<script setup lang="ts">
import { ref } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import { Input } from '@/components/ui/input';
import { useForm, router } from '@inertiajs/vue3';
import { Project } from '@/types'



const props = defineProps<{
    project: Project,
}>()

const open = ref(false);

const formatToISO = (date: Date) => {
  return date.toISOString().split('T')[0];
}

// 🗓️ Fechas actuales
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(today.getDate() + 1);

const todayFormatted = formatToISO(today);
const tomorrowFormatted = formatToISO(tomorrow);

// 🚀 Formulario
const form = useForm({
  name: '',
  goal: '',
  start_date: todayFormatted,
  end_date: tomorrowFormatted,
  project_id: props.project.id,
});

const submit = () => {
  if (form.start_date < todayFormatted) {
    alert('Start date cannot be before today.');
    return;
  }

  if (form.end_date < form.start_date) {
    alert('End date cannot be before start date.');
    return;
  }

  form.post('/sprints', {
    onSuccess: () => {
      form.reset();
      open.value = false;
      router.reload();
    }
  });
}
</script>

<template>
  <div>
    <!-- Botón -->
    <Button @click="open = true" class="border-blue-500 text-white bg-blue-500 hover:bg-blue-600">
      Create Sprint
    </Button>

    <!-- Modal -->
    <Dialog :open="open" @update:open="open = $event">
      <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
        <!-- Título -->
        <DialogTitle class="text-lg font-bold mb-4 text-gray-800">New Sprint</DialogTitle>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" />
          </div>

          <!-- Goal -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Goal</label>
            <Input v-model="form.goal" class="w-full border-gray-300 text-black bg-white" />
          </div>

          <!-- Start Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <Input
              v-model="form.start_date"
              type="date"
              :min="todayFormatted"
              class="w-full border-gray-300 text-black bg-white"
            />
          </div>

          <!-- End Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <Input
              v-model="form.end_date"
              type="date"
              :min="form.start_date"
              class="w-full border-gray-300 text-black bg-white"
            />
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-2">
            <Button
              type="button"
              variant="secondary"
              @click="open = false"
              class="bg-gray-200 text-gray-800 hover:bg-gray-300"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-500 text-white hover:bg-blue-600"
            >
              Save
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
