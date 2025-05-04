<script setup lang="ts">
import { ref } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import { router, useForm } from '@inertiajs/vue3';
import Input from './ui/input/Input.vue';
import Swal from 'sweetalert2';

interface User {
    id: number,
    name: string,
    status: string,
    email: string,
    password: string,
    nickname: string,
    hour_value: number,
    work_time: string
}

const props = defineProps<{
    user: User
}>()

const open = ref(false)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    nickname: props.user.nickname,
    password: '',
    hour_value: props.user.hour_value,
    work_time: props.user.work_time,
    status: props.user.status
})

const disableInput = () => {
    return props.user.status === 'active' ? false : true;
}

router.put(`/users/${props.user.id}`, form, {
    preserveScroll: true,

    // Se ejecuta si no hubo errores de validación y el backend respondió bien
    onSuccess: () => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'User Updated',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        form.reset();
        open.value = false;
        router.reload(); // Recarga los datos si es necesario
    },

    // Se ejecuta si el backend devuelve errores de validación (422)
    onError: (errors) => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Error',
            text: Object.values(errors).join(', '),
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    },

    // Se ejecuta si la solicitud falla por algo inesperado (500, 404, etc.)
    onFinish: () => {
        console.log('La petición finalizó (éxito o error)');
    },
});
</script>
<template>
    <div>
        <Button @click="open = true" class="bg-blue-500 mt-3 text-white hover:bg-blue-600">
            View more
        </Button>

        <Dialog :open="open" @update:open="open = $event">
            <DialogContent class="max-w-md p-6 bg-white rounded-lg shadow-lg">
                <DialogTitle class="text-lg font-bold mb-4 text-gray-800"> {{ props.user.name }}</DialogTitle>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <Input v-model="form.name" class="w-full border-gray-300 text-black bg-white" :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nick name</label>
                        <Input v-model="form.nickname" class="w-full border-gray-300 text-black bg-white capitalize" :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <Input v-model="form.email" class="w-full border-gray-300 text-black bg-white" :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <Input v-model="form.password" placeholder="If you forget your password, enter a new one." class="w-full border-gray-300 text-black bg-white" :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hour value</label>
                        <Input v-model="form.hour_value" class="w-full border-gray-300 text-black bg-white" :disabled="disableInput()" />
                    </div>
                    <div class="flex space-x-4 justify-start">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 bg-white text-black"
                            >
                                <option value="status" disabled>Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Work time</label>
                            <select
                                v-model="form.work_time"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 bg-white text-black"
                                :disabled="disableInput()"
                            >
                                <option value="status" disabled>Status</option>
                                <option value="part time">Part time</option>
                                <option value="full">Full</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="secondary" @click="open = false" class="bg-gray-200 text-gray-800 hover:bg-gray-300">
                            Close
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