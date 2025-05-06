<script setup lang="ts">
import UpdateUserModal from './UpdateUserModal.vue';



type UserStatus = 'active' | 'inactive' | 'completed' | 'cancelled' | 'paused'

interface User {
    id: number,
    name: string,
    status: UserStatus,
    email: string
    hour_value: number
    work_time: string
}

const props = defineProps<{
    user: User
}>()


function getStatusClass(status: UserStatus): string {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'inactive':
            return 'bg-gray-100 text-gray-600'
        default:
            return  'bg-gray-100 text-gray-600'
    }
}
</script>
<template>
    <div class="maxx-w-xs mx-auto mt-6">
        <div class="bg-background border border-foreground/200 rounded-2xl shadow-md p-6">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-xl font-bold">
                    {{ props.user.name.length > 12
                    ? props.user.name.slice(0, 10) + '...'
                    : props.user.name }}
                </h2>
                <span
                    class="px-3 py-1 text-sm rounded-full capitalize"
                    :class="getStatusClass(props.user.status)"
                >
                    {{ props.user.status }}
                </span>
            </div>
            <p class="text-gray-600 m-1">
                <span class="font-bold">Email:</span>
                {{ props.user.email.length > 15
                ? props.user.email.slice(0, 15) + '...'
                : props.user.email }}
            </p>
            <p class="text-gray-600 m-1">
                <span class="font-bold">Hour value: </span>
                {{ props.user.hour_value }}
            </p>
            <p class="text-gray-600 m-1 capitalize">
               <span class="font-bold">Work time:</span>
               {{ props.user.work_time }}
            </p>
            <div class="w-full flex justify-end">
                <UpdateUserModal
                    :user="props.user"
                />
            </div>
        </div>
    </div>
</template>