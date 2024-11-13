<!-- resources/js/Pages/Users/Index.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import Modal from '@/Components/Shared/Modal.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object
});

const search = ref(props.filters.search);
const selectedRole = ref(props.filters.role);
const showDeleteModal = ref(false);
const userToDelete = ref(null);

watch([search, selectedRole], debounce(() => {
    router.get(route('users.index'), {
        search: search.value,
        role: selectedRole.value
    }, { preserveState: true });
}, 300));

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        }
    });
};

const getStatusColor = (isActive) => {
    return isActive
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};
</script>

<template>
    <AppLayout>
        <PageHeader title="User Management" description="Manage system users and their roles">
            <template #actions>
                <Link v-if="$page.props.can.create_users" :href="route('users.create')">
                <Button variant="primary">
                    Add User
                </Button>
                </Link>
            </template>
        </PageHeader>

        <Card>
            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" v-model="search"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Search users..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select v-model="selectedRole"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Roles</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ user.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.role.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    getStatusColor(user.is_active)
                                ]">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(user.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link v-if="$page.props.can.edit_users" :href="route('users.edit', user.id)"
                                    class="text-primary-600 hover:text-primary-900 mr-3">
                                Edit
                                </Link>
                                <button v-if="$page.props.can.delete_users && user.id !== $page.props.auth.user.id"
                                    @click="confirmDelete(user)" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <!-- Add pagination component -->
            </div>
        </Card>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Confirm Delete
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete this user? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <Button variant="secondary" @click="showDeleteModal = false">
                        Cancel
                    </Button>
                    <Button variant="danger" @click="deleteUser">
                        Delete User
                    </Button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>