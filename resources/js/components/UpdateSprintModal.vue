<script setup lang="ts">
import { ref, watch } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import { useForm, router } from '@inertiajs/vue3';
import Input from './ui/input/Input.vue';

interface Sprint {
    id: number,
    name: string
    goal: string
    start_date: string
    end_date: string
}
const props = defineProps<{
    sprint: Sprint
    project_id: number
}>()


const open = ref(false);

watch (open, (isOpen) => {
    if (isOpen) {
        form.name = props.sprint.name;
        form.goal = props.sprint.goal;
        form.start_date = props.sprint.start_date.split('T')[0];
        form.end_date = props.sprint.end_date.split('T')[0];
    }
})

const formatToISO = (date: Date) => {
  return date.toISOString().split('T')[0];
}

const today = new Date()
const todayFormatted = formatToISO(today);

const form = useForm({
    name: props.sprint.name,
    goal: props.sprint.goal,
    start_date: props.sprint.start_date.split('T')[0],
    end_date: props.sprint.end_date.split('T')[0]
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

    form.put(`/projects/${props.project_id}/sprints/${props.sprint.id}`, {
        onSuccess: () => {
            form.reset();
            open.value = false;
            router.reload();
        },
    });
}
</script>
<template>
    <div >
        <Button @click="open = true" class="border-black bg-black text-white hover:bg-gray-400 hover:border-white hover:text-black">
            Update Sprint
        </Button>
        
        <Dialog :open="open" @update:open="open = $event">
            <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
                <DialogTitle class="text-lg font-bold mb-4 text-gray-800">Update Sprint</DialogTitle>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Goal</label>
                        <Input v-model="form.goal" class="w-full border-gray-300 text-black bg-white" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start date</label>
                        <Input v-model="form.start_date" :min="todayFormatted" type="date" class="w-full border-gray-300 text-black bg-white" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End date</label>
                        <Input v-model="form.end_date" :min="form.start_date" type="date" class="w-full border-gray-300 text-black bg-white" />
                    </div>
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
