<!-- resources/js/Pages/Inventory/Index.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import debounce from 'lodash/debounce';
import { formatCurrency } from '@/Utils/format';

const props = defineProps({
    items: Object,
    filters: Object,
    categories: Array
});

const search = ref(props.filters.search);
const selectedCategory = ref(props.filters.category);

watch([search, selectedCategory], debounce(() => {
    router.get(route('inventory.index'), {
        search: search.value,
        category: selectedCategory.value
    }, { preserveState: true });
}, 300));
</script>

<template>
    <AppLayout>
        <PageHeader title="Inventory Management" description="Manage your restaurant's inventory items">
            <template #actions>
                <Link :href="route('inventory.items.create')" v-if="$page.props.can.create_items">
                <Button variant="primary">
                    Add New Item
                </Button>
                </Link>
            </template>
        </PageHeader>

        <Card>
            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Search</label>
                    <input type="text" v-model="search"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                        placeholder="Search items..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Category</label>
                    <select v-model="selectedCategory"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600">
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-dark-500">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Item
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                SKU
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Stock
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-dark-300 dark:divide-gray-600">
                        <tr v-for="item in items.data" :key="item.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ item.name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-50">
                                            {{ item.vendor.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-50">
                                {{ item.sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-50">
                                {{ item.category.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div :class="[
                                    'text-sm',
                                    Math.round(item.current_stock) <= Math.round(item.minimum_stock)
                                        ? 'text-red-600'
                                        : 'text-gray-900'
                                ]">
                                    {{ Math.round(item.current_stock) }} {{ item.unit }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-50">
                                    Min: {{ Math.round(item.minimum_stock) }} {{ item.unit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ formatCurrency(item.price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link v-if="$page.props.can.edit_items" :href="route('inventory.items.edit', item.id)"
                                    class="text-primary-600 hover:text-primary-900 dark:text-dark-600">
                                Edit
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <!-- Add pagination component here -->
            </div>
        </Card>
    </AppLayout>
</template>