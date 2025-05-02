<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import Input from './ui/input/Input.vue';
import { router, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps<{
  style: string,
  task_id: number,
  project_id: number,
  sprint: any,
}>()

const open = ref(false);
const form = useForm({
  id: '',
  name: '',
  description: '',
  status: '',
  priority: '',
  category: '',
  story_points: '',
  user_id: '',
  user_name: '',
  estimated_start: '',
  estimated_finish: '',
  estimated_hours: '',
  actual_start: '',
  actual_finish: '',
  actual_hours: '',
});

const filled_data = ref({})

const isLocked = (field: keyof typeof form) => {
  return !!filled_data.value?.[field];
};

const lockedClass = (field: keyof typeof form) => {
  return isLocked(field) ? 'text-gray-600 cursor-not-allowed' : 'text-black';
};

const searchTask = async () => {
  try {
    const response = await fetch(`/tasks/${props.task_id}`);
    if (!response.ok) throw Error('Failed to fetch task data');

    const data = await response.json();

    form.id = data.id
    form.name = data.name;
    form.description = data.description;
    form.status = data.status;
    form.priority = data.priority;
    form.category = data.category;
    form.story_points = data.story_points;
    form.user_id = data.user?.id ?? userLog.props.auth.user.id;
    form.user_name = data.user?.name ?? userLog.props.auth.user.name + " (You)"
    form.estimated_start = data.estimated_start?.split('T')[0];
    form.estimated_finish = data.estimated_finish?.split('T')[0];
    form.estimated_hours = data.estimated_hours;
    form.actual_start = data.actual_start?.split('T')[0];
    form.actual_finish = data.actual_finish?.split('T')[0];
    form.actual_hours = data.actual_hours;

    filled_data.value = data
  } catch (error) {
    console.error(error);
  }
};

const userLog = usePage();

const cleanFormData = () => {
  Object.keys(form.data()).forEach((key) => {
    if (form[key] === '' || form[key] === undefined) {
      form[key] = null;
    }
  });
};

const submit = async () => {
  cleanFormData();

  try {
    await form.put(`/tasks/${props.task_id}`, {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();         
        open.value = false;   
        router.reload();      

        // Toast de éxito
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Task updated successfully',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
          }
        });
      },
      onError: () => {
        // Toast de error si falla la validación u otro problema del backend
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Failed to update task',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      }
    });
  } catch (error) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Unexpected error occurred',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
  }
};

console.log(filled_data.value);

</script>

<template>
  <div>
    <Button @click="() => { open = true; searchTask(); }" :class="`px-2 py-1 rounded ${props.style}`">
      View more
    </Button>

    <Dialog :open="open" @update:open="open = $event">
      <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
        <DialogTitle class="text-lg font-bold text-gray-800">
          Task: {{ form.id }} . {{ form.name.length > 25 ? form.name.slice(0, 25) + '...' : form.name }}
        </DialogTitle>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              class="text-gray-500 border rounded w-full max-h-40 overflow-auto resize-none bg-white p-3"
              readonly
              v-text="form.description"
            />
          </div>

          <hr>
          <h2 class="text-black font-bold">Estimates</h2>
          <div class="flex justify-start space-x-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Date start</label>
              <Input
                v-model="form.estimated_start"
                type="date"
                :min="props.sprint.start_date.split('T')[0]"
                :max="props.sprint.end_date.split('T')[0]"
                :readonly="isLocked('estimated_start')"
                :class="['max-w-sm border-gray-300 bg-white', lockedClass('estimated_start')]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Date finish</label>
              <Input
                v-model="form.estimated_finish"
                type="date"
                :min="form.estimated_start"
                :max="props.sprint.end_date.split('T')[0]"
                :readonly="isLocked('estimated_finish')"
                :class="['border-gray-300 bg-white', lockedClass('estimated_finish')]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Hours</label>
              <Input
                v-model="form.estimated_hours"
                readonly
                :class="['w-full border-gray-300 bg-white', lockedClass('estimated_hours')]"
              />
            </div>
          </div>

          <hr>
          <div class="flex justify-around space-x-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <Input 
                v-model="form.status"
                readonly
                :class="['w-full border-gray-300 bg-white', lockedClass('status')]"
               />
              <!-- <select
                v-model="form.status"
                :disabled="isLocked('priority')"
                :class="['border h-9 border-gray-300 rounded bg-white px-2', lockedClass('status')]"
              >
                <option value="to do" disabled>To do</option>
                <option value="in progress">In progress</option>
                <option value="done">Done</option>
              </select> -->
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
              <Input 
                v-model="form.priority"
                readonly
                :class="['w-full border-gray-300 bg-white', lockedClass('status')]"
               />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <Input 
                v-model="form.category"
                readonly
                :class="['w-full border-gray-300 bg-white', lockedClass('status')]"
               />
            </div>
          </div>

          <hr>
          <template v-if="form.estimated_finish && form.estimated_start">
            <h2 class="text-black font-bold">Actual</h2>
            <div class="flex justify-around space-x-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Date start</label>
                <Input
                  v-model="form.actual_start"
                  type="date"
                  :min="props.sprint.start_date.split('T')[0]"
                  :max="props.sprint.end_date.split('T')[0]"
                  readonly
                  :class="['w-full border-gray-300 bg-white', lockedClass('actual_start')]"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Date finish</label>
                <Input
                  v-model="form.actual_finish"
                  type="date"
                  :min="form.actual_start"
                  :max="props.sprint.end_date.split('T')[0]"
                  readonly
                  :class="['w-full border-gray-300 bg-white', lockedClass('actual_finish')]"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Hours</label>
                <Input
                  v-model="form.actual_hours"
                  type="number"
                  min="1"
                  :readonly="isLocked('actual_hours')"
                  :class="['w-full border-gray-300 bg-white', lockedClass('actual_hours')]"
                />
              </div>
            </div>
          </template>          

          <div>
            <label class="block text-sm font-medium text-gray-700">Story points</label>
            <Input
              v-model="form.story_points"
              type="number"
              placeholder="0"
              min="0"
              max="10"
              :readonly="isLocked('story_points')"
              :class="['w-full border-gray-300 bg-white', lockedClass('story_points')]"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Assigned</label>
            <Input
                v-model="form.user_name"
                :class="['w-full border-gray-300 bg-white', lockedClass('user_id')]"
                readonly
            />
            <input type="hidden" v-model="form.user_id" />
          </div>

          <div class="flex justify-end gap-2">
            <Button type="button" variant="secondary" @click="open = false" class="bg-gray-200 text-gray-800 hover:bg-gray-300">
              Close
            </Button>
            <Button type="submit" :disabled="form.processing" class="bg-blue-500 text-white hover:bg-blue-600"
              v-if="form.status !== 'done'">
              Update
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
