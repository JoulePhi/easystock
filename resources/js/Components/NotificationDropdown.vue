<!-- resources/js/Components/NotificationDropdown.vue -->
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { BellIcon } from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';
import { formatDistanceToNow } from 'date-fns';

const notifications = ref([]);
const unreadCount = ref(0);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.unread'));
        notifications.value = response.data;
        unreadCount.value = response.data.length;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const markAsRead = async (id) => {
    try {
        await axios.post(route('notifications.mark-as-read', id));
        notifications.value = notifications.value.filter(n => n.id !== id);
        unreadCount.value -= 1;
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const getNotificationContent = (notification) => {
    switch (notification.data.type) {
        case 'low_stock':
            return {
                title: 'Low Stock Alert',
                message: `${notification.data.item_name} is below minimum stock level`,
                link: route('inventory.items.show', notification.data.item_id)
            };
        case 'new_order':
            return {
                title: 'New Order Received',
                message: `Order #${notification.data.order_number} has been created`,
                link: route('sales.show', notification.data.order_id)
            };
        case 'purchase_order_status':
            return {
                title: 'Purchase Order Update',
                message: `PO #${notification.data.po_number} is ${notification.data.status}`,
                link: route('purchase-orders.show', notification.data.purchase_order_id)
            };
        default:
            return {
                title: 'Notification',
                message: 'You have a new notification',
                link: route('notifications.index')
            };
    }
};

onMounted(() => {
    fetchNotifications();
    // Refresh notifications every minute
    window.Echo.private(`notifications.${$page.props.auth.user.id}`)
        .listen('NewNotification', (e) => {
            notifications.value.unshift(e.notification);
            unreadCount.value += 1;
        });
    setInterval(fetchNotifications, 60000);
});

onBeforeUnmount(() => {
    window.Echo.leave(`notifications.${$page.props.auth.user.id}`);
});
</script>

<template>
    <Menu as="div" class="relative">
        <MenuButton
            class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <span class="sr-only">View notifications</span>
            <BellIcon class="h-6 w-6" />
            <span v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-medium text-white">
                {{ unreadCount }}
            </span>
        </MenuButton>

        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="py-1">
                    <div class="px-4 py-2 text-sm font-medium text-gray-700 border-b border-gray-200">
                        Notifications
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <div v-if="notifications.length === 0" class="px-4 py-3 text-sm text-gray-500">
                            No new notifications
                        </div>

                        <MenuItem v-else v-for="notification in notifications" :key="notification.id"
                            v-slot="{ active }">
                        <Link :href="getNotificationContent(notification).link" :class="[
                            active ? 'bg-gray-100' : '',
                            'block px-4 py-3 cursor-pointer'
                        ]" @click="markAsRead(notification.id)">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ getNotificationContent(notification).title }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ getNotificationContent(notification).message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ formatDistanceToNow(new Date(notification.created_at), { addSuffix: true }) }}
                                </p>
                            </div>
                        </div>
                        </Link>
                        </MenuItem>
                    </div>

                    <div v-if="notifications.length > 0" class="border-t border-gray-200">
                        <Link :href="route('notifications.index')"
                            class="block px-4 py-2 text-sm text-center text-primary-600 hover:text-primary-900">
                        View All Notifications
                        </Link>
                    </div>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>