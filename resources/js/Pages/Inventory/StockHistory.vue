<!-- resources/js/Components/Inventory/StockHistory.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    itemId: {
        type: [Number, String],
        required: true
    }
});

const transactions = ref([]);
const loading = ref(true);

const fetchHistory = async () => {
    try {
        const response = await axios.get(route('inventory.stock.history', props.itemId));
        transactions.value = response.data;
    } catch (error) {
        console.error('Failed to fetch stock history:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHistory();
});
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-900">Stock History</h3>

        <div v-if="loading" class="text-center py-4">
            Loading...
        </div>

        <div v-else-if="transactions.length === 0" class="text-center py-4 text-gray-500">
            No transactions found
        </div>

        <div v-else class="flow-root">
            <ul role="list" class="-mb-8">
                <!-- resources/js/Components/Inventory/StockHistory.vue (lanjutan) -->
                <li v-for="(transaction, index) in transactions" :key="transaction.id">
                    <div class="relative pb-8">
                        <span v-if="index !== transactions.length - 1"
                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                        <div class="relative flex space-x-3">
                            <div>
                                <span :class="[
                                    'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white',
                                    transaction.type.code.includes('ADD') || transaction.type.code === 'PUR'
                                        ? 'bg-green-500'
                                        : 'bg-red-500'
                                ]">
                                    <component :is="transaction.type.code.includes('ADD') ? PlusIcon : MinusIcon"
                                        class="h-5 w-5 text-white" aria-hidden="true" />
                                </span>
                            </div>
                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        {{ transaction.quantity > 0 ? '+' : '' }}{{ transaction.quantity }}
                                        {{ transaction.item.unit }} via
                                        <span class="font-medium text-gray-900">
                                            {{ transaction.type.name }}
                                        </span>
                                    </p>
                                    <p v-if="transaction.notes" class="mt-1 text-sm text-gray-500">
                                        {{ transaction.notes }}
                                    </p>
                                </div>
                                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                    <time :datetime="transaction.created_at">
                                        {{ new Date(transaction.created_at).toLocaleString() }}
                                    </time>
                                    <div class="mt-1 text-xs">
                                        by {{ transaction.user.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>