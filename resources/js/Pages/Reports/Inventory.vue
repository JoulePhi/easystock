<!-- resources/js/Pages/Reports/Inventory.vue -->
<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';
import DateRangePicker from '@/Components/Reports/DateRangePicker.vue';
import { Bar, Doughnut } from 'vue-chartjs';
import { formatCurrency } from '@/Utils/format';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    items: Array,
    stockMovements: Object,
    lowStockItems: Array,
    stockValue: Number,
    dateRange: String
});

const dateRange = ref(props.dateRange);
const activeTab = ref('overview');

// Stock Movement Chart
const stockMovementData = computed(() => ({
    labels: props.items.map(item => item.name),
    datasets: [
        {
            label: 'Incoming',
            data: props.items.map(item => props.stockMovements[item.id]?.incoming || 0),
            backgroundColor: '#10b981',
        },
        {
            label: 'Outgoing',
            data: props.items.map(item => props.stockMovements[item.id]?.outgoing || 0),
            backgroundColor: '#ef4444',
        }
    ]
}));

// Stock Value by Category Chart
const categoryValueData = computed(() => {
    const categoryValues = props.items.reduce((acc, item) => {
        const category = item.category.name;
        acc[category] = (acc[category] || 0) + (item.current_stock * item.cost);
        return acc;
    }, {});

    return {
        labels: Object.keys(categoryValues),
        datasets: [{
            data: Object.values(categoryValues),
            backgroundColor: [
                '#3b82f6',
                '#10b981',
                '#f59e0b',
                '#ef4444',
                '#8b5cf6',
                '#ec4899'
            ]
        }]
    };
});

// Summary calculations
const totalItems = computed(() => props.items.length);
const totalStockValue = computed(() => props.stockValue);
const lowStockCount = computed(() => props.lowStockItems.length);

const updateDateRange = (newRange) => {
    router.get(route('reports.inventory'), {
        date_range: newRange
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const exportReport = () => {
    window.location.href = route('reports.inventory.export');
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Inventory Report" description="Track inventory levels and movements">
            <template #actions>
                <div class="flex items-center space-x-4">
                    <div class="w-72">
                        <DateRangePicker v-model="dateRange" @update:modelValue="updateDateRange" />
                    </div>
                    <Button v-if="$page.props.can.export_reports" variant="secondary" @click="exportReport">
                        Export Report
                    </Button>
                </div>
            </template>
        </PageHeader>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Items
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ totalItems }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Stock Value
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(totalStockValue) }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Low Stock Items
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ lowStockCount }}
                    </dd>
                </div>
            </Card>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button v-for="tab in ['overview', 'stock-levels', 'movements']" :key="tab" @click="activeTab = tab"
                    :class="[
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
        <div v-if="activeTab === 'overview'" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Stock Value by Category -->
            <Card>
                <template #title>Stock Value by Category</template>
                <div class="p-4">
                    <Doughnut :data="categoryValueData" :options="{ responsive: true }" />
                </div>
            </Card>

            <!-- Low Stock Items -->
            <Card>
                <template #title>Low Stock Items</template>
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
                                    Current Stock
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Minimum Stock
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in lowStockItems" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ item.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ item.category.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-red-600 font-medium">
                                        {{ item.current_stock }} {{ item.unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.minimum_stock }} {{ item.unit }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>

        <div v-if="activeTab === 'stock-levels'" class="space-y-6">
            <!-- Stock Levels Table -->
            <Card>
                <template #title>Current Stock Levels</template>
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
                                    Category
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Stock
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in items" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ item.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ item.sku }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.category.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'text-sm font-medium',
                                        item.current_stock <= item.minimum_stock
                                            ? 'text-red-600'
                                            : 'text-gray-900'
                                    ]">
                                        {{ item.current_stock }} {{ item.unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(item.current_stock * item.cost) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>

        <div v-if="activeTab === 'movements'" class="space-y-6">
            <!-- Stock Movements Chart -->
            <Card>
                <template #title>Stock Movements</template>
                <div class="p-4">
                    <Bar :data="stockMovementData" :options="{
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    }" />
                </div>
            </Card>
        </div>
    </AppLayout>
</template>