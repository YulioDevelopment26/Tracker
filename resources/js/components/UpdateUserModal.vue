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

const submit = () => {
    router.put(`/users/${props.user.id}`, form, {
        preserveScroll: true,

        // Se ejecuta si no hubo errores de validación y el backend respondió bien
        onSuccess: (page) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: page.props.flash?.message || 'User Updated',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            form.reset();
            open.value = false;
            router.reload();
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

        // Se ejecuta si la solicitud finaliza (éxito o error)
        onFinish: () => {
            console.log('La petición finalizó (éxito o error)');
        },
    });
}; // <- Esta llave faltaba

</script>
<template>
    <div>
        <Button @click="open = true" class="mt-2">
            View more
        </Button>

        <Dialog :open="open" @update:open="open = $event">
            <DialogContent class="max-w-md p-6  rounded-lg shadow-lg">
                <DialogTitle class="text-lg font-bold mb-4 "> {{ props.user.name }}</DialogTitle>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <Input v-model="form.name" class="w-full border-gray-300 " :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nick name</label>
                        <Input v-model="form.nickname" class="w-full border-gray-300  capitalize" :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <Input v-model="form.email" class="w-full border-gray-300 " :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <Input v-model="form.password" placeholder="If you forget your password, enter a new one." class="w-full border-gray-300 " :disabled="disableInput()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Hour value</label>
                        <Input v-model="form.hour_value" class="w-full border-gray-300 " :disabled="disableInput()" />
                    </div>
                    <div class="flex space-x-4 justify-start">
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full bg-background border border-foreground/200 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 "
                            >
                                <option value="status" disabled>Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Work time</label>
                            <select
                                v-model="form.work_time"
                                class="w-full bg-background border border-foreground/200  rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 "
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
                        <Button type="button" variant="secondary" @click="open = false" >
                            Close
                        </Button>
                        <Button type="submit" :disabled="form.processing" >
                            Update
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>