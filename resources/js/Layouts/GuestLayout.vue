<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen items-center bg-white  sm:justify-center sm:pt-0 dark:bg-dark-500">
        <div class="w-1/2 flex justify-center items-center">
            <Link href="/">
            <ApplicationLogo class="h-52 w-5h-52 fill-current text-gray-500" />
            </Link>
        </div>
        <div class="w-1/2 flex justify-center items-center">
            <div class="w-full overflow-hidden  px-6  sm:max-w-md sm:rounded-lg ">
                <slot />
            </div>
        </div>

    </div>
</template>
