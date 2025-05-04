<script setup lang="ts">
import { ref } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import Input from './ui/input/Input.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const form = useForm({
    name: '',
    nickname: '',
    email: '',
    hour_value: '',
    status: '',
    work_time: ''
})

const open = ref(false)

const submit = () => {
    router.post('/users', form, {
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Usuario creado correctamente',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            form.reset();
            open.value = false;
        },
        onError: (errors) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Error al crear el usuario',
                text: Object.values(errors).join(', '),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    });
};
</script>
<template>
    <div>
        <Button @click="open = true" class="border-black bg-black text-white hover:bg-gray-400 hover:border-white hover:text-black">
            New User
        </Button>

        <Dialog :open="open" @update:open="open = $event">
            <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
                <DialogTitle class="text-lg font-bold mb-4 text-gray-800">New User</DialogTitle>
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Name -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" />
                     </div>
                     <!-- Nickname -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                        <Input v-model="form.nickname" class="w-full border-gray-300 text-black bg-white" />
                     </div>
                     <!-- Email -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <Input v-model="form.email" type="email" class="w-full border-gray-300 text-black bg-white" />
                     </div>
                     <!-- hour value -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hour Value</label>
                        <Input v-model="form.hour_value" type="number" min="0" class="w-full border-gray-300 text-black bg-white" />
                     </div>
                     <!-- work time -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Work time</label>
                        <select
                            v-model="form.work_time"
                            class=" border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 bg-white text-black border"
                        >
                            <option disabled selected hidden>Status</option>
                            <option value="part time">Part time</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
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