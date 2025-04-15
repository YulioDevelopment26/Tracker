<script setup lang="ts">
import Button from './ui/button/Button.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import Input from './ui/input/Input.vue';

interface Sprint {
    id: number,
    goal: string,
    name: string,
    start_date: string,
    end_date: string,
}

const props = defineProps<{
    sprint: Sprint
    project_id: number
}>()

const open = ref(false);

const form = useForm({
    name: '',
    description: '',
    priority: '',
    category: '',
    story_points: 0,
    sprint_id: props.sprint.id,
    project_id: props.project_id,
    estimated_hours: 1,
});

const submit = () => {
    console.log(form)
    form.post('/tasks', {
        onSuccess: () => {
            form.reset();
            open.value = false;
            router.reload()
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        }
    });
}

const start_sprint = new Date(props.sprint.start_date)
const end_sprint = new Date(props.sprint.end_date)

const diffDate = end_sprint.getTime() - start_sprint.getTime()
const diffDays = (diffDate / (1000 * 60 * 60 * 24)) * 24;
</script>

<template>
    <div>
        <Button @click="open = true" class="border-blue-500 text-white bg-blue-500 hover:bg-blue-600">
            Create Task
        </Button>
        
        <Dialog :open="open" @update:open="open = $event">
            <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
                <DialogTitle class="text-lg font-bold mb-4 text-gray-800">New Task</DialogTitle>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <Input v-model="form.description" class="w-full border-gray-300 text-black bg-white" />
                    </div>

                    <div class="flex justify-start space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select v-model="form.priority" class="border h-9 border-gray-300 rounded text-black bg-white px-2">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category" class="border h-9 border-gray-300 rounded text-black bg-white px-2">
                                <option value="frontend">Front-end</option>
                                <option value="backend">Back-end</option>
                                <option value="full stack">Full Stack</option>
                                <option value="design">Design</option>
                                <option value="deployment">Deployment</option>
                                <option value="fixes">Fixes</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated hours</label>
                        <Input
                            v-model="form.estimated_hours"
                            type="number"
                            min="1"
                            :max="diffDays"
                            class="w-full border-gray-300 text-black bg-white"
                        />
                    </div>


                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="secondary" @click="open = false" class="bg-gray-200 text-gray-800 hover:bg-gray-300">
                            Cancel
                        </Button>
                        <Button type="submit" :disable="form.processing" class="bg-blue-500 text-white hover:bg-blue-600">
                            Save
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
