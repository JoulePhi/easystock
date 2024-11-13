<!-- resources/js/Pages/Sales/Index.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import debounce from 'lodash/debounce';
import { formatCurrency, formatDate } from '@/Utils/format';

const props = defineProps({
    orders: Object,
    filters: Object
});

const search = ref(props.filters.search);
const selectedStatus = ref(props.filters.status);
const dateRange = ref(props.filters.date_range);

watch([search, selectedStatus, dateRange], debounce(() => {
    router.get(route('sales.index'), {
        search: search.value,
        status: selectedStatus.value,
        date_range: dateRange.value
    }, { preserveState: true });
}, 300));

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Sales & Orders" description="Manage sales and orders">
            <template #actions>
                <Link v-if="$page.props.can.create_orders" :href="route('sales.create')">
                <Button variant="primary">
                    New Order
                </Button>
                </Link>
            </template>
        </PageHeader>

        <Card>
            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Search</label>
                    <input type="text" v-model="search"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                        placeholder="Search order number..." />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Status</label>
                    <select v-model="selectedStatus"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Date Range</label>
                    <input type="text" v-model="dateRange"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                        placeholder="Select date range" />
                </div>
            </div>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-dark-500">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Order Number
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Date
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Total
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Payment
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Link :href="route('sales.show', order.id)"
                                    class="text-primary-600 hover:text-primary-900">
                                {{ order.order_number }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    getStatusColor(order.status)
                                ]">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(order.total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    order.payment?.status === 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ order.payment?.status || 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('sales.receipt', order.id)"
                                    class="text-primary-600 hover:text-primary-900 mr-3" target="_blank">
                                Receipt
                                </Link>
                                <Link :href="route('sales.show', order.id)"
                                    class="text-primary-600 hover:text-primary-900">
                                View
                                </Link>
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
    </AppLayout>
</template>