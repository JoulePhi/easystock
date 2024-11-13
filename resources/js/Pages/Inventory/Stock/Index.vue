<!-- resources/js/Pages/Inventory/Stock/Index.vue -->
<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import Modal from '@/Components/Shared/Modal.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    transactions: Object,
    transactionTypes: Array,
    filters: Object
});

const showAdjustModal = ref(false);
const selectedItem = ref(null);

const form = useForm({
    item_id: '',
    type_id: '',
    quantity: 0,
    unit_price: 0,
    notes: ''
});

const search = ref(props.filters.search);
const selectedType = ref(props.filters.type);

watch([search, selectedType], debounce(() => {
    router.get(route('inventory.stock.index'), {
        search: search.value,
        type: selectedType.value
    }, { preserveState: true });
}, 300));

const openAdjustModal = (item) => {
    selectedItem.value = item;
    form.reset();
    form.item_id = item.id;
    showAdjustModal.value = true;
};

const submitAdjustment = () => {
    form.post(route('inventory.stock.adjust'), {
        onSuccess: () => {
            showAdjustModal.value = false;
        }
    });
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Stock Management" description="View and manage stock transactions">
            <template #actions>
                <Button v-if="$page.props.can.adjust_stock" @click="showAdjustModal = true" variant="primary">
                    Adjust Stock
                </Button>
            </template>
        </PageHeader>

        <Card>
            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" v-model="search"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Search items..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                    <select v-model="selectedType"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Types</option>
                        <option v-for="type in transactionTypes" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="transaction in transactions.data" :key="transaction.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(transaction.created_at).toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ transaction.item.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ transaction.item.sku }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    transaction.type.code.includes('ADD') || transaction.type.code === 'PUR'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                ]">
                                    {{ transaction.type.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ transaction.quantity > 0 ? '+' : '' }}{{ transaction.quantity }}
                                {{ transaction.item.unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ transaction.user.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ transaction.notes }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Stock Adjustment Modal -->
        <Modal :show="showAdjustModal" @close="showAdjustModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Adjust Stock
                </h3>

                <form @submit.prevent="submitAdjustment" class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select v-model="form.type_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Select Type</option>
                            <option v-for="type in transactionTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.type_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.type_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" v-model="form.quantity" step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                        <p v-if="form.errors.quantity" class="mt-1 text-sm text-red-600">
                            {{ form.errors.quantity }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                        <input type="number" v-model="form.unit_price" step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                        <p v-if="form.errors.unit_price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.unit_price }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea v-model="form.notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <Button type="button" variant="secondary" @click="showAdjustModal = false">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary" :disabled="form.processing">
                            Save Adjustment
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>