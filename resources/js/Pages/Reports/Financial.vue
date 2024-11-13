<!-- resources/js/Pages/Reports/Financial.vue -->
<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';
import DateRangePicker from '@/Components/Reports/DateRangePicker.vue';
import { Line, Bar, Doughnut } from 'vue-chartjs';
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
    revenue: Number,
    cogs: Number,
    grossProfit: Number,
    dailyRevenue: Object,
    categoryPerformance: Array,
    dateRange: String
});

const dateRange = ref(props.dateRange);

// Revenue Chart Data
const revenueChartData = computed(() => ({
    labels: Object.keys(props.dailyRevenue),
    datasets: [{
        label: 'Daily Revenue',
        data: Object.values(props.dailyRevenue),
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37, 99, 235, 0.1)',
        fill: true
    }]
}));

const revenueChartOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Revenue ($)'
            }
        }
    }
};

// Category Performance Chart Data
const categoryChartData = computed(() => ({
    labels: props.categoryPerformance.map(cat => cat.name),
    datasets: [{
        label: 'Revenue by Category',
        data: props.categoryPerformance.map(cat => cat.revenue),
        backgroundColor: [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#ec4899'
        ]
    }]
}));

// Profit Margin
const profitMargin = computed(() =>
    props.revenue > 0 ? (props.grossProfit / props.revenue) * 100 : 0
);

const updateDateRange = (newRange) => {
    router.get(route('reports.financial'), {
        date_range: newRange
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const exportReport = () => {
    window.location.href = route('reports.financial.export', {
        date_range: dateRange.value
    });
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Financial Report" description="View financial performance and metrics">
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

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4 mb-6">
            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Revenue
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(revenue) }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Cost of Goods Sold
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(cogs) }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Gross Profit
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ formatCurrency(grossProfit) }}
                    </dd>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Profit Margin
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ profitMargin.toFixed(1) }}%
                    </dd>
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Revenue Trend -->
            <Card>
                <template #title>Revenue Trend</template>
                <div class="p-4">
                    <Line :data="revenueChartData" :options="revenueChartOptions" />
                </div>
            </Card>

            <!-- Category Performance -->
            <Card>
                <template #title>Revenue by Category</template>
                <div class="p-4">
                    <Doughnut :data="categoryChartData" :options="{ responsive: true }" />
                </div>
            </Card>

            <!-- Category Performance Table -->
            <Card>
                <template #title>Category Performance</template>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Revenue
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Avg. Order Value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categoryPerformance" :key="category.name">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ category.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(category.revenue) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ category.order_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(category.revenue / category.order_count) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>