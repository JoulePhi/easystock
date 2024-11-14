<!-- resources/js/Pages/Sales/Show.vue -->
<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import Modal from '@/Components/Shared/Modal.vue';
import { formatCurrency, formatDate } from '@/Utils/format';

const props = defineProps({
    order: {
        type: Object,
        required: true
    }
});

const showVoidModal = ref(false);

const voidOrder = () => {
    router.post(route('sales.void', props.order.id), {}, {
        onSuccess: () => {
            showVoidModal.value = false;
        }
    });
};

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
        <PageHeader :title="`Order: ${order.order_number}`"
            :description="`Created by ${order.user.name} on ${formatDate(order.created_at)}`">
            <template #actions>
                <div class="space-x-3">
                    <Link :href="route('sales.receipt', order.id)" target="_blank">
                    <Button variant="secondary">
                        Print Receipt
                    </Button>
                    </Link>

                    <Button v-if="$page.props.can.void_orders && order.status === 'completed'" variant="danger"
                        @click="showVoidModal = true">
                        Void Order
                    </Button>
                </div>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <Card>
                    <template #title>Order Items</template>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit Price
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ item.item.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ item.item.sku }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ item.quantity }} {{ item.item.unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(item.unit_price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(item.total_price) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                        Subtotal
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(order.subtotal) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                        Tax
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(order.tax) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-base font-medium text-gray-900 text-right">
                                        Total
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-gray-900">
                                        {{ formatCurrency(order.total_amount) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </Card>

                <Card v-if="order.notes">
                    <template #title>Notes</template>
                    <p class="text-gray-600">{{ order.notes }}</p>
                </Card>
            </div>

            <!-- Order Summary -->
            <div class="space-y-6">
                <Card>
                    <template #title>Order Status</template>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    getStatusColor(order.status)
                                ]">
                                    {{ order.status }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Created By</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ order.user.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Created At</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ formatDate(order.created_at) }}
                            </div>
                        </div>
                    </div>
                </Card>

                <Card>
                    <template #title>Payment Information</template>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Method</label>
                            <div class="mt-1 text-sm text-gray-900 capitalize">
                                {{ order.payment?.payment_method || 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Status</label>
                            <div class="mt-1">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    order.payment?.status === 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ order.payment?.status || 'Pending' }}
                                </span>
                            </div>
                        </div>

                        <div v-if="order.payment?.amount_paid">
                            <label class="text-sm font-medium text-gray-500">Amount Paid</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ formatCurrency(order.payment.amount_paid) }}
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>

        <!-- Void Order Modal -->
        <Modal :show="showVoidModal" @close="showVoidModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Void Order
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to void this order? This action cannot be undone, and inventory will be
                    returned to
                    stock.
                </p>
                <div class="flex justify-end space-x-3">
                    <Button variant="secondary" @click="showVoidModal = false">
                        Cancel
                    </Button>
                    <Button variant="danger" @click="voidOrder">
                        Void Order
                    </Button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>