<!-- resources/js/Pages/Inventory/Categories/Index.vue -->
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import Modal from '@/Components/Shared/Modal.vue';

const props = defineProps({
    categories: Object
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
    description: ''
});

const createForm = () => {
    form.reset();
    showCreateModal.value = true;
};

const editForm = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.description = category.description;
    showEditModal.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('inventory.categories.update', editingCategory.value.id), {
            onSuccess: () => {
                showEditModal.value = false;
                editingCategory.value = null;
            }
        });
    } else {
        form.post(route('inventory.categories.store'), {
            onSuccess: () => {
                showCreateModal.value = false;
            }
        });
    }
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Categories" description="Manage inventory categories">
            <template #actions>
                <Button v-if="$page.props.can.create_categories" @click="createForm" variant="primary">
                    Add Category
                </Button>
            </template>
        </PageHeader>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items Count
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="category in categories.data" :key="category.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ category.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ category.description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ category.items_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button v-if="$page.props.can.edit_categories" @click="editForm(category)"
                                    class="text-primary-600 hover:text-primary-900 mr-3">
                                    Edit
                                </button>
                                <button v-if="$page.props.can.delete_categories" @click="deleteCategory(category)"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Create/Edit Modal -->
        <Modal :show="showCreateModal || showEditModal" @close="showCreateModal = showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ editingCategory ? 'Edit Category' : 'Create Category' }}
                </h3>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" v-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <Button type="button" variant="secondary" @click="showCreateModal = showEditModal = false">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary" :disabled="form.processing">
                            {{ editingCategory ? 'Update' : 'Create' }}
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>