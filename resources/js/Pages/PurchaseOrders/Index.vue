<!-- resources/js/Pages/PurchaseOrders/Index.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import debounce from 'lodash/debounce';
import { formatCurrency } from '@/Utils/format';

const props = defineProps({
    purchaseOrders: Object,
    filters: Object,
    statuses: Object
});

const search = ref(props.filters.search);
const selectedStatus = ref(props.filters.status);

watch([search, selectedStatus], debounce(() => {
    router.get(route('purchase-orders.index'), {
        search: search.value,
        status: selectedStatus.value
    }, { preserveState: true });
}, 300));

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-100 text-gray-800',
        sent: 'bg-blue-100 text-blue-800',
        received: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Purchase Orders" description="Manage purchase orders to vendors">
            <template #actions>
                <Link v-if="$page.props.can.create_purchase_orders" :href="route('purchase-orders.create')">
                <Button variant="primary">
                    Create Purchase Order
                </Button>
                </Link>
            </template>
        </PageHeader>

        <Card>
            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Search</label>
                    <input type="text" v-model="search"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                        placeholder="Search PO number or vendor..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Status</label>
                    <select v-model="selectedStatus"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600">
                        <option value="">All Statuses</option>
                        <option v-for="(label, value) in statuses" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-dark-500">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                PO Number
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Vendor
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Amount
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Expected Date
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="po in purchaseOrders.data" :key="po.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Link :href="route('purchase-orders.show', po.id)"
                                    class="text-primary-600 hover:text-primary-900">
                                {{ po.po_number }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ po.vendor.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    getStatusColor(po.status)
                                ]">
                                    {{ statuses[po.status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(po.total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(po.expected_date).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('purchase-orders.pdf', po.id)"
                                    class="text-primary-600 hover:text-primary-900 mr-3" target="_blank">
                                PDF
                                </Link>
                                <Link v-if="$page.props.can.edit_purchase_orders"
                                    :href="route('purchase-orders.show', po.id)"
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