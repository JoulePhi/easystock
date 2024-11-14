<!-- resources/js/Components/Layout/AppLayout.vue -->
<script setup>
import { onMounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bars3Icon,
    XMarkIcon,
    HomeIcon,
    ShoppingCartIcon,
    CubeIcon,
    ChartBarIcon,
    CogIcon,
    ClipboardDocumentListIcon,
    UserIcon,
    ArrowRightStartOnRectangleIcon,
} from '@heroicons/vue/24/outline';
import NotificationDropdown from '../Components/Shared/NotificationDropdown.vue';
const showingNavigationDropdown = ref(false);
const { user } = usePage().props;
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ToggleTheme from '@/Components/Shared/ToggleTheme.vue';
import { toast } from 'vue3-toastify';



const navigation = [
    { name: 'Dashboard', href: 'dashboard', icon: HomeIcon, permission: 'view_dashboard' },
    { name: 'Inventory', href: 'inventory.index', icon: CubeIcon, permission: 'view_inventory' },
    { name: 'Purchase Orders', href: 'purchase-orders.index', icon: ShoppingCartIcon, permission: 'view_purchase_orders' },
    { name: 'Sales', href: 'sales.index', icon: ClipboardDocumentListIcon, permission: 'view_orders' },
    { name: 'Reports', href: 'reports.index', icon: ChartBarIcon, permission: 'view_sales_reports' },
    // { name: 'Settings', href: route('settings.index'), icon: CogIcon, permission: 'manage_settings' },
];

const userNavigation = [
    { name: 'Your Profile', href: route('profile.edit') },
    // { name: 'Settings', href: route('settings.index') },
];

const page = usePage()

onMounted(() => {
    if (page.props.flash.success) {
        toast.success(page.props.flash.success)
    }
    if (page.props.flash.error) {
        toast.error(page.props.flash.error)
    }
})
</script>

<template>
    <div>
        <!-- Off-canvas menu for mobile -->
        <div class="lg:hidden">
            <!-- ... mobile menu code ... -->
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col shadow">
            <div class="flex flex-grow flex-col overflow-y-auto bg-white pt-5 dark:bg-dark-400">
                <div class="flex flex-shrink-0 items-center justify-center px-4">
                    <ApplicationLogo class="h-8 w-5h-8 fill-current text-primary-500" />
                </div>
                <nav class="mt-10 flex flex-1 flex-col px-2">
                    <div class="space-y-5">
                        <Link v-for="item in navigation" :key="item.name" :href="route(item.href)" :class="[
                            route().current(item.href)
                                ? 'text-primary-500 dark:text-dark-600'
                                : 'text-grey-600 dark:text-white hover:text-primary-500 dark:hover:text-dark-600',
                            'group flex items-center rounded-md px-2 py-2  font-medium'
                        ]">
                        <component :is="item.icon" :class="[
                            route().current(item.href)
                                ? 'text-primary-500 dark:text-dark-600'
                                : 'text-grey-600 dark:text-white group-hover:text-primary-500 dark:group-hover:text-dark-600',
                            'mr-3 h-6 w-6 flex-shrink-0'
                        ]" />
                        {{ item.name }}
                        </Link>
                    </div>
                    <div class="h-full"></div>
                    <Link :href="route('logout')" :class="[
                        route().current('logout')
                            ? 'text-primary-500 dark:text-dark-600'
                            : 'text-grey-600 dark:text-white hover:text-primary-500 dark:hover:text-dark-600',
                        'group flex items-center rounded-md px-2 py-5 mb-4  font-medium'
                    ]">
                    <component :is="ArrowRightStartOnRectangleIcon" :class="[
                        route().current('logout')
                            ? 'text-primary-500 dark:text-dark-600'
                            : 'text-grey-600 dark:text-white group-hover:text-primary-500 dark:group-hover:text-dark-600',
                        'mr-3 h-6 w-6 flex-shrink-0'
                    ]" />
                    Logout
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <div class="flex flex-col">
                <!-- Top navbar -->
                <div
                    class="flex h-16 flex-shrink-0 border-b border-gray-200 bg-white dark:bg-dark-400 dark:border-dark-400">
                    <button type="button"
                        class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 lg:hidden"
                        @click="showingNavigationDropdown = true">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <!-- User dropdown and notifications -->
                    <div class="flex flex-1 justify-between px-4">
                        <div class="flex flex-1"></div>
                        <div class="ml-4 flex items-center md:ml-6">
                            <ToggleTheme />
                            <NotificationDropdown />
                            <!-- User dropdown -->
                            <div class="relative ml-3">
                                <div>
                                    <button type="button"
                                        class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        <span class="sr-only">Open user menu</span>
                                        <UserIcon class="h-8 w-8 rounded-full text-gray-400" />
                                    </button>
                                </div>
                                <!-- Dropdown menu -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content area -->
                <main class="flex-1">
                    <div class="py-6">
                        <div class="mx-auto px-4 sm:px-6 md:px-8">
                            <slot />
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>