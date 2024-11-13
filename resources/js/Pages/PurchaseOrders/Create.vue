<!-- resources/js/Pages/PurchaseOrders/Create.vue -->
<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import { formatCurrency } from '@/Utils/format';

const props = defineProps({
    vendors: Array,
    items: Array
});

const form = useForm({
    vendor_id: '',
    expected_date: '',
    notes: '',
    items: [
        {
            item_id: '',
            quantity: 1,
            unit_price: 0
        }
    ]
});

const selectedItems = ref(new Set());

const addItem = () => {
    form.items.push({
        item_id: '',
        quantity: 1,
        unit_price: 0
    });
};

const removeItem = (index) => {
    const itemId = form.items[index].item_id;
    if (itemId) {
        selectedItems.value.delete(itemId);
    }
    form.items.splice(index, 1);
};

const getItemDetails = (itemId) => {
    return props.items.find(item => item.id == itemId);
};

const onItemSelect = (index, itemId) => {
    const oldItemId = form.items[index].item_id;
    if (oldItemId) {
        selectedItems.value.delete(oldItemId);
    }

    if (itemId) {
        selectedItems.value.add(itemId);
        const item = getItemDetails(itemId);
        form.items[index].unit_price = item.cost;
    }
};

const availableItems = computed(() => {
    return props.items.filter(item => !selectedItems.value.has(item.id));
});

const totalAmount = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_price);
    }, 0);
});

const submit = () => {
    form.post(route('purchase-orders.store'));
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Create Purchase Order" description="Create a new purchase order for vendor">
            <template #actions>
                <Link :href="route('purchase-orders.index')">
                <Button variant="secondary">
                    Cancel
                </Button>
                </Link>
            </template>
        </PageHeader>

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <template #title>Basic Information</template>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vendor</label>
                            <select v-model="form.vendor_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :class="{ 'border-red-500': form.errors.vendor_id }">
                                <option value="">Select Vendor</option>
                                <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                                    {{ vendor.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.vendor_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.vendor_id }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Expected Date</label>
                            <input type="date" v-model="form.expected_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :class="{ 'border-red-500': form.errors.expected_date }" />
                            <p v-if="form.errors.expected_date" class="mt-1 text-sm text-red-600">
                                {{ form.errors.expected_date }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea v-model="form.notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                    </div>
                </Card>

                <!-- Items -->
                <Card>
                    <template #title>Items</template>
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.items" :key="index" class="border-b border-gray-200 pb-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Item</label>
                                    <select v-model="item.item_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.item_id`] }"
                                        @change="onItemSelect(index, item.item_id)">
                                        <option value="">Select Item</option>
                                        <option v-for="availableItem in availableItems" :key="availableItem.id"
                                            :value="availableItem.id">
                                            {{ availableItem.name }} ({{ availableItem.sku }})
                                        </option>
                                        <option v-if="item.item_id && !availableItems.find(i => i.id == item.item_id)"
                                            :value="item.item_id">
                                            {{ getItemDetails(item.item_id).name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors[`items.${index}.item_id`]" class="mt-1 text-sm text-red-600">
                                        {{ form.errors[`items.${index}.item_id`] }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input type="number" v-model="item.quantity" min="1" step="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.quantity`] }" />
                                    <p v-if="form.errors[`items.${index}.quantity`]" class="mt-1 text-sm text-red-600">
                                        {{ form.errors[`items.${index}.quantity`] }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                                    <input type="number" v-model="item.unit_price" min="0" step="0.01"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.unit_price`] }" />
                                    <p v-if="form.errors[`items.${index}.unit_price`]"
                                        class="mt-1 text-sm text-red-600">
                                        {{ form.errors[`items.${index}.unit_price`] }}
                                    </p>
                                </div>

                                <div class="flex items-end justify-end">
                                    <Button v-if="form.items.length > 1" type="button" variant="danger"
                                        @click="removeItem(index)">
                                        Remove
                                    </Button>
                                </div>
                            </div>

                            <!-- Item Subtotal -->
                            <div class="mt-2 text-right text-sm text-gray-600">
                                Subtotal: {{ formatCurrency(item.quantity * item.unit_price) }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <Button type="button" variant="secondary" @click="addItem">
                                Add Item
                            </Button>

                            <div class="text-lg font-medium">
                                Total: {{ formatCurrency(totalAmount) }}
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <Button type="button" variant="secondary" :disabled="form.processing"
                        @click="$inertia.visit(route('purchase-orders.index'))">
                        Cancel
                    </Button>
                    <Button type="submit" variant="primary" :disabled="form.processing">
                        Create Purchase Order
                    </Button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>