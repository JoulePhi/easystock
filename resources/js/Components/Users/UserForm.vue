<!-- resources/js/Components/Users/UserForm.vue -->
<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Shared/Button.vue';

const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    roles: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    role_id: props.user?.role_id ?? '',
    is_active: props.user?.is_active ?? true
});

const submit = () => {
    if (props.user) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div>
            <InputLabel for="password" :value="user ? 'New Password (leave blank to keep current)' : 'Password'" />
            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" :required="!user"
                autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div>
            <InputLabel for="role" value="Role" />
            <select id="role" v-model="form.role_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Select Role</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                </option>
            </select>
            <InputError class="mt-2" :message="form.errors.role_id" />
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="is_active" v-model="form.is_active"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50" />
            <label for="is_active" class="ml-2 text-sm text-gray-600">
                Active
            </label>
        </div>

        <div class="flex justify-end space-x-3">
            <Button type="button" variant="secondary" :href="route('users.index')">
                Cancel
            </Button>
            <Button type="submit" variant="primary" :disabled="form.processing">
                {{ user ? 'Update User' : 'Create User' }}
            </Button>
        </div>
    </form>
</template>
