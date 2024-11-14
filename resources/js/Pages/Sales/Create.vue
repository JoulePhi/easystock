<!-- resources/js/Pages/Sales/Create.vue -->
<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import { formatCurrency } from '@/Utils/format';
import { PlusIcon, MinusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    items: Object,
    tax_rate: Number
});

const form = useForm({
    items: [],
    payment_method: 'cash',
    amount_paid: 0,
    notes: ''
});

const activeCategory = ref(Object.keys(props.items)[0] || '');
const searchQuery = ref('');
const showPaymentModal = ref(false);

// Cart computations
const subtotal = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (item.quantity * item.price);
    }, 0);
});

const tax = computed(() => subtotal.value * props.tax_rate);
const total = computed(() => subtotal.value + tax.value);
const change = computed(() => form.amount_paid - total.value);

// Filter items by search query
const filteredItems = computed(() => {
    const items = props.items[activeCategory.value] || [];
    if (!searchQuery.value) return items;

    return items.filter(item =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Add item to cart
const addToCart = (item) => {
    const existingItem = form.items.find(i => i.item_id === item.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        form.items.push({
            item_id: item.id,
            name: item.name,
            price: item.price,
            quantity: 1
        });
    }
};

// Update item quantity
const updateQuantity = (index, quantity) => {
    if (quantity <= 0) {
        form.items.splice(index, 1);
    } else {
        form.items[index].quantity = quantity;
    }
};

// Process payment
const processPayment = () => {
    form.post(route('sales.store'), {
        onSuccess: () => {
            showPaymentModal.value = false;
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="h-[calc(100vh-4rem)] flex">
            <!-- Left Side - Items Selection -->
            <div class="w-2/3 p-6 bg-gray-50 overflow-y-auto">
                <!-- Search and Categories -->
                <div class="mb-6">
                    <input type="text" v-model="searchQuery"
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Search items..." />
                </div>

                <div class="flex space-x-2 overflow-x-auto mb-6">
                    <button v-for="category in Object.keys(items)" :key="category" @click="activeCategory = category"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap',
                            activeCategory === category
                                ? 'bg-primary-600 text-white'
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]">
                        {{ category }}
                    </button>
                </div>

                <!-- Items Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <button v-for="item in filteredItems" :key="item.id" @click="addToCart(item)"
                        class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-200 h-72"
                        :class="{ 'opacity-50 cursor-not-allowed': item.current_stock <= 0 }"
                        :disabled="item.current_stock <= 0">
                        <img :src="item.image_url" alt="" class="rounded-lg h-2/3 object-cover mx-auto" />
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-500">{{ formatCurrency(item.price) }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Stock: {{ item.current_stock }} {{ item.unit }}
                        </div>
                    </button>
                </div>
            </div>

            <!-- Right Side - Cart -->
            <div class="w-1/3 border-l border-gray-200 flex flex-col">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Current Order</h2>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div v-if="form.items.length === 0" class="text-center text-gray-500">
                        No items in cart
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(item, index) in form.items" :key="index" class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ item.name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ formatCurrency(item.price) }} x {{ item.quantity }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="updateQuantity(index, item.quantity - 1)"
                                    class="p-1 rounded-full hover:bg-gray-100">
                                    <MinusIcon class="w-4 h-4" />
                                </button>
                                <span class="w-8 text-center">{{ item.quantity }}</span>
                                <button @click="updateQuantity(index, item.quantity + 1)"
                                    class="p-1 rounded-full hover:bg-gray-100">
                                    <PlusIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal</span>
                            <span>{{ formatCurrency(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax ({{ (props.tax_rate * 100).toFixed(0) }}%)</span>
                            <span>{{ formatCurrency(tax) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-medium pt-2 border-t">
                            <span>Total</span>
                            <span>{{ formatCurrency(total) }}</span>
                        </div>
                    </div>

                    <Button class="w-full mt-6" variant="primary" :disabled="form.items.length === 0"
                        @click="showPaymentModal = true">
                        Process Payment
                    </Button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <Modal :show="showPaymentModal" @close="showPaymentModal = false" max-width="md">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">
                    Process Payment
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Payment Method
                        </label>
                        <select v-model="form.payment_method"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    <div v-if="form.payment_method === 'cash'">
                        <label class="block text-sm font-medium text-gray-700">
                            Amount Received
                        </label>
                        <input type="number" v-model="form.amount_paid"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            step="0.01" min="0" />
                        <div class="mt-2 text-sm text-gray-500">
                            Change: {{ formatCurrency(change) }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Notes
                        </label>
                        <textarea v-model="form.notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <Button variant="secondary" @click="showPaymentModal = false">
                            Cancel
                        </Button>
                        <Button variant="primary"
                            :disabled="form.processing || (form.payment_method === 'cash' && form.amount_paid < total)"
                            @click="processPayment">
                            Complete Order
                        </Button>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>