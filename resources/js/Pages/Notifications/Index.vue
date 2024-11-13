<!-- resources/js/Pages/Notifications/Index.vue -->
<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';
import { formatDistanceToNow } from 'date-fns';

const props = defineProps({
    notifications: Object
});

const getNotificationIcon = (type) => {
    switch (type) {
        case 'low_stock':
            return 'ExclamationCircleIcon';
        case 'new_order':
            return 'ShoppingCartIcon';
        case 'purchase_order_status':
            return 'TruckIcon';
        default:
            return 'BellIcon';
    }
};

const getNotificationContent = (notification) => {
    const data = notification.data;
    switch (data.type) {
        case 'low_stock':
            return {
                title: 'Low Stock Alert',
                message: `${data.item_name} is below minimum stock level (${data.current_stock} ${data.unit} remaining)`,
                link: route('inventory.items.show', data.item_id)
            };
        case 'new_order':
            return {
                title: 'New Order Received',
                message: `Order #${data.order_number} has been created for ${formatCurrency(data.total_amount)}`,
                link: route('sales.show', data.order_id)
            };
        case 'purchase_order_status':
            return {
                title: 'Purchase Order Update',
                message: `PO #${data.po_number} from ${data.vendor_name} is ${data.status}`,
                link: route('purchase-orders.show', data.purchase_order_id)
            };
        default:
            return {
                title: 'Notification',
                message: 'You have a new notification',
                link: '#'
            };
    }
};

const markAllAsRead = () => {
    router.post(route('notifications.mark-all-read'));
};

const deleteNotification = (id) => {
    router.delete(route('notifications.destroy', id));
};
</script>

<template>
    <AppLayout>
        <PageHeader title="Notifications" description="View and manage your notifications">
            <template #actions>
                <Button v-if="notifications.data.some(n => !n.read_at)" variant="secondary" @click="markAllAsRead">
                    Mark All as Read
                </Button>
            </template>
        </PageHeader>

        <Card>
            <div class="divide-y divide-gray-200">
                <div v-if="notifications.data.length === 0" class="p-6 text-center text-gray-500">
                    No notifications found
                </div>

                <div v-for="notification in notifications.data" :key="notification.id" :class="[
                    'p-6',
                    { 'bg-gray-50': !notification.read_at }
                ]">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <component :is="getNotificationIcon(notification.data.type)"
                                class="h-6 w-6 text-gray-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ getNotificationContent(notification).title }}
                                </p>
                                <div class="ml-4 flex-shrink-0 flex">
                                    <button @click="deleteNotification(notification.id)"
                                        class="text-gray-400 hover:text-gray-500">
                                        <XIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ getNotificationContent(notification).message }}
                            </p>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-gray-400">
                                    {{ formatDistanceToNow(new Date(notification.created_at), { addSuffix: true }) }}
                                </p>
                                <Link :href="getNotificationContent(notification).link"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-900">
                                View Details
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Card>
    </AppLayout>
</template>