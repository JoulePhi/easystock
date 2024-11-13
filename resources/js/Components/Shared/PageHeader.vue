<script setup>
import { Head } from '@inertiajs/vue3'
defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    }
});
</script>

<template>

    <Head :title="title" />
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2
                class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight dark:text-white">
                {{ title }}
            </h2>
            <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-dark-600">
                {{ description }}
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <slot name="actions"></slot>
        </div>
    </div>
</template>