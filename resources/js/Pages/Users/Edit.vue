<!-- Update resources/js/Pages/Users/Edit.vue -->
<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import UserForm from '@/Components/Users/UserForm.vue';

const props = defineProps({
    user: Object,
    roles: Array,
    activities: Object
});

const activeTab = ref('details');
</script>

<template>
    <AppLayout>
        <PageHeader :title="`Edit User: ${user.name}`" description="Update user information and role" />

        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button v-for="tab in ['details', 'activity']" :key="tab" @click="activeTab = tab" :class="[
                    activeTab === tab
                        ? 'border-primary-500 text-primary-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                    'whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm'
                ]">
                    {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div v-if="activeTab === 'details'">
            <Card>
                <UserForm :user="user" :roles="roles" />
            </Card>
        </div>

        <div v-else-if="activeTab === 'activity'">
            <Card>
                <template #title>Activity Log</template>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date/Time
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="activity in activities.data" :key="activity.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(activity.created_at).toLocaleString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        {
                                            'bg-green-100 text-green-800': activity.action === 'created',
                                            'bg-blue-100 text-blue-800': activity.action === 'updated',
                                            'bg-red-100 text-red-800': activity.action === 'deleted'
                                        }
                                    ]">
                                        {{ activity.action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ activity.description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ activity.ip_address }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>