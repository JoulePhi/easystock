<!-- resources/js/Pages/Profile/Edit.vue -->
<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Shared/Button.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    sessions: Array
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.patch(route('profile.update'));
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Profile Settings" description="Manage your account settings and security preferences" />

        <div class="space-y-6">
            <!-- Profile Information -->
            <Card>
                <template #title>Profile Information</template>
                <form @submit.prevent="updateProfile" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="profileForm.name" required
                            autofocus />
                        <InputError class="mt-2" :message="profileForm.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="profileForm.email"
                            required />
                        <InputError class="mt-2" :message="profileForm.errors.email" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" variant="primary" :disabled="profileForm.processing">
                            Save Changes
                        </Button>
                    </div>
                </form>
            </Card>

            <!-- Update Password -->
            <Card>
                <template #title>Update Password</template>
                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div>
                        <InputLabel for="current_password" value="Current Password" />
                        <TextInput id="current_password" type="password" class="mt-1 block w-full"
                            v-model="passwordForm.current_password" required />
                        <InputError class="mt-2" :message="passwordForm.errors.current_password" />
                    </div>

                    <div>
                        <InputLabel for="password" value="New Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full"
                            v-model="passwordForm.password" required />
                        <InputError class="mt-2" :message="passwordForm.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="passwordForm.password_confirmation" required />
                        <InputError class="mt-2" :message="passwordForm.errors.password_confirmation" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" variant="primary" :disabled="passwordForm.processing">
                            Update Password
                        </Button>
                    </div>
                </form>
            </Card>

            <!-- Active Sessions -->
            <Card v-if="sessions.length > 0">
                <template #title>Active Sessions</template>
                <div class="space-y-6">
                    <div v-for="(session, i) in sessions" :key="i" class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ session.agent.platform }} - {{ session.agent.browser }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ session.ip_address }}
                                <span class="text-green-500" v-if="session.is_current_device">
                                    (Current device)
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Last active {{ new Date(session.last_active).toLocaleString() }}
                            </div>
                        </div>
                        <Button v-if="!session.is_current_device" variant="danger" @click="logout(session.id)">
                            Logout
                        </Button>
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>