<!-- resources/js/Pages/Reports/Index.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';

const reports = [
    {
        title: 'Sales Report',
        description: 'View detailed sales analysis, top-selling items, and payment methods breakdown',
        icon: 'CurrencyDollarIcon',
        route: 'reports.sales',
        permission: 'view_sales_reports'
    },
    {
        title: 'Inventory Report',
        description: 'Track stock levels, movements, and inventory valuation',
        icon: 'CubeIcon',
        route: 'reports.inventory',
        permission: 'view_inventory_reports'
    },
    {
        title: 'Financial Report',
        description: 'Analyze revenue, costs, and profitability',
        icon: 'ChartBarIcon',
        route: 'reports.financial',
        permission: 'view_financial_reports'
    }
];
</script>

<template>
    <AppLayout>
        <PageHeader title="Reports" description="Access and export various business reports" />

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Link v-for="report in reports" :key="report.title" :href="route(report.route)"
                v-show="$page.props.can[report.permission]">
            <Card class="h-full hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <component :is="report.icon" class="h-6 w-6 text-primary-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ report.title }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-dark-600">
                            {{ report.description }}
                        </p>
                    </div>
                </div>
            </Card>
            </Link>
        </div>
    </AppLayout>
</template>