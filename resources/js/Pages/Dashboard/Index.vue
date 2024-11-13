<!-- resources/js/Pages/Dashboard/Index.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalSales: 0,
            totalOrders: 0,
            lowStockItems: 0,
            pendingPurchaseOrders: 0
        })
    }
});
</script>

<template>
    <AppLayout>
        <PageHeader title="Dashboard" description="Overview of your restaurant's performance">
            <template #actions>
                <Button variant="primary">
                    Refresh Data
                </Button>
            </template>
        </PageHeader>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-dark-600 truncate">
                        Total Sales Today
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        ${{ stats.totalSales.toLocaleString() }}
                    </div>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-dark-600 truncate">
                        Orders Today
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ stats.totalOrders }}
                    </div>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-dark-600 truncate">
                        Low Stock Items
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ stats.lowStockItems }}
                    </div>
                </div>
            </Card>

            <Card>
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-dark-600 truncate">
                        Pending Purchase Orders
                    </div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ stats.pendingPurchaseOrders }}
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>