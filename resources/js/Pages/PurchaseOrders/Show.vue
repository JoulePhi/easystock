<!-- resources/js/Pages/PurchaseOrders/Show.vue -->
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import Modal from '@/Components/Shared/Modal.vue';
import { formatCurrency, formatDate } from '@/utils/format';

const props = defineProps({
    purchaseOrder: {
        type: Object,
        required: true
    }
});

const showUpdateModal = ref(false);
const form = useForm({
    status: props.purchaseOrder.status,
    expected_date: props.purchaseOrder.expected_date,
    received_date: props.purchaseOrder.received_date,
    notes: props.purchaseOrder.notes
});

const updateStatus = () => {
    form.put(route('purchase-orders.update', props.purchaseOrder.id), {
        onSuccess: () => {
            showUpdateModal.value = false;
        }
    });
};

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
        <PageHeader :title="`Purchase Order: ${purchaseOrder.po_number}`"
            :description="`Created by ${purchaseOrder.user.name} on ${formatDate(purchaseOrder.created_at)}`">
            <template #actions>
                <div class="space-x-3">
                    <Link :href="route('purchase-orders.pdf', purchaseOrder.id)" target="_blank">
                    <Button variant="secondary">
                        Download PDF
                    </Button>
                    </Link>

                    <Button
                        v-if="$page.props.can.edit_purchase_orders && ['draft', 'sent'].includes(purchaseOrder.status)"
                        variant="primary" @click="showUpdateModal = true">
                        Update Status
                    </Button>
                </div>
            </template>
        </PageHeader>

        <div class="space-y-6">
            <!-- Basic Information -->
            <Card>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Vendor Information</h3>
                        <div class="mt-3 space-y-1">
                            <p class="text-sm text-gray-500">Name: {{ purchaseOrder.vendor.name }}</p>
                            <p class="text-sm text-gray-500">Email: {{ purchaseOrder.vendor.email }}</p>
                            <p class="text-sm text-gray-500">Phone: {{ purchaseOrder.vendor.phone }}</p>
                            <p class="text-sm text-gray-500">Contact Person: {{ purchaseOrder.vendor.contact_person }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Order Details</h3>
                        <div class="mt-3 space-y-1">
                            <p class="text-sm text-gray-500">
                                Status:
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    getStatusColor(purchaseOrder.status)
                                ]">
                                    {{ purchaseOrder.status }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-500">Expected Date: {{ formatDate(purchaseOrder.expected_date)
                                }}</p>
                            <p v-if="purchaseOrder.received_date" class="text-sm text-gray-500">
                                Received Date: {{ formatDate(purchaseOrder.received_date) }}
                            </p>
                            <p class="text-sm text-gray-500">Notes: {{ purchaseOrder.notes || 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Items -->
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
                                    SKU
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
                            <tr v-for="item in purchaseOrder.items" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ item.item.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.item.sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                                <td colspan="4" class="px-6 py-4 text-right font-medium">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(purchaseOrder.total_amount) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </Card>
        </div>

        <!-- Update Status Modal -->
        <Modal :show="showUpdateModal" @close="showUpdateModal = false" max-width="md">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Update Purchase Order Status
                </h3>

                <form @submit.prevent="updateStatus" class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent to Vendor</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expected Date</label>
                        <input type="date" v-model="form.expected_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                    </div>

                    <div v-if="form.status === 'received'">
                        <label class="block text-sm font-medium text-gray-700">Received Date</label>
                        <input type="date" v-model="form.received_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea v-model="form.notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <Button type="button" variant="secondary" @click="showUpdateModal = false">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary" :disabled="form.processing">
                            Update
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>