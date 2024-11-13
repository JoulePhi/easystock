<!-- resources/js/Components/Layout/NotificationDropdown.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { BellIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const notifications = ref([]);
const unreadCount = ref(0);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unreadCount;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
};

const markAsRead = async (notification) => {
    try {
        await axios.post(route('notifications.markAsRead', notification.id));
        notification.read_at = new Date();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

onMounted(() => {
    fetchNotifications();
});
</script>

<template>
    <Menu as="div" class="relative ml-3">
        <div>
            <MenuButton
                class="relative rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <span class="sr-only">View notifications</span>
                <BellIcon class="h-6 w-6" aria-hidden="true" />
                <span v-if="unreadCount > 0"
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center h-4 w-4 rounded-full bg-red-500 text-xs font-bold text-white">
                    {{ unreadCount }}
                </span>
            </MenuButton>
        </div>
        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="px-4 py-2 text-sm font-medium text-gray-700 border-b">
                    Notifications
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <MenuItem v-if="notifications.length === 0" v-slot="{ active }">
                    <div class="px-4 py-3 text-sm text-gray-500">
                        No notifications
                    </div>
                    </MenuItem>
                    <MenuItem v-for="notification in notifications" :key="notification.id" v-slot="{ active }">
                    <div @click="markAsRead(notification)" :class="[
                        active ? 'bg-gray-100' : '',
                        'px-4 py-3 cursor-pointer'
                    ]">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ notification.data.item_name }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Current stock ({{ notification.data.current_stock }} {{ notification.data.unit }})
                                    is below minimum ({{ notification.data.minimum_stock }} {{ notification.data.unit
                                    }})
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ new Date(notification.created_at).toLocaleString() }}
                                </p>
                            </div>
                            <div v-if="!notification.read_at" class="ml-3">
                                <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                            </div>
                        </div>
                    </div>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>