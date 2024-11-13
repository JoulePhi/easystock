<!-- resources/js/Pages/Reports/Sales.vue -->
<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';
import DateRangePicker from '@/Components/Reports/DateRangePicker.vue';
import { Line, Pie, Bar } from 'vue-chartjs';
import { formatCurrency } from '@/Utils/format';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    dailySales: Object,
    topItems: Array,
    paymentMethods: Array,
    dateRange: String
});

const dateRange = ref(props.dateRange);

// Chart data
const salesChartData = computed(() => ({
    labels: Object.keys(props.dailySales),
    datasets: [
        {
            label: 'Daily Sales',
            data: Object.values(props.dailySales).map(day => day.total_sales),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            fill: true
        },
        {
            label: 'Number of Orders',
            data: Object.values(props.dailySales).map(day => day.total_orders),
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.1)',
            fill: true,
            yAxisID: 'y1'
        }
    ]
}));

const salesChartOptions = {
    responsive: true,
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Sales Amount ($)'
            }
        },
        y1: {
            beginAtZero: true,
            position: 'right',
            title: {
                display: true,
                text: 'Number of Orders'
            },
            grid: {
                drawOnChartArea: false
            }
        }
    }
};

const paymentMethodsChartData = computed(() => ({
    labels: props.paymentMethods.map(pm => pm.payment_method.toUpperCase()),
    datasets: [{
        data: props.paymentMethods.map(pm => pm.total_amount),
        backgroundColor: [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444'
        ]
    }]
}));

// Summary calculations
const totalSales = computed(() =>
    Object.values(props.dailySales).reduce((sum, day) => sum + day.total_sales, 0)
);

const totalOrders = computed(() =>
    Object.values(props.dailySales).reduce((sum, day) => sum + day.total_orders, 0)
);

const averageOrderValue = computed(() =>
    totalOrders.value ? totalSales.value / totalOrders.value : 0
);

// Handle date range change
const updateDateRange = (newRange) => {
    router.get(route('reports.sales'), { date_range: newRange }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Export report
const exportReport = () => {
    window.location.href = route('reports.sales.export', {
        date_range: dateRange.value
    });
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Sales Report" description="Analyze sales performance and trends">
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
                        Total Sales
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(totalSales) }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Orders
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ totalOrders }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Average Order Value
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(averageOrderValue) }}
                    </dd>
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Sales Trend Chart -->
            <Card>
                <template #title>Sales Trend</template>
                <div class="p-4">
                    <Line :data="salesChartData" :options="salesChartOptions" />
                </div>
            </Card>

            <!-- Payment Methods Chart -->
            <Card>
                <template #title>Payment Methods</template>
                <div class="p-4">
                    <Pie :data="paymentMethodsChartData" :options="{ responsive: true }" />
                </div>
            </Card>

            <!-- Top Selling Items -->
            <Card>
                <template #title>Top Selling Items</template>
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
                                    Quantity Sold
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Sales
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in topItems" :key="item.name">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.total_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(item.total_sales) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>