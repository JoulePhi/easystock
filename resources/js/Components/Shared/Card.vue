<script setup>
defineProps({
    title: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-dark-400">
        <div v-if="title" class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ title }}
            </h3>
            <slot name="header"></slot>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <slot></slot>
        </div>
        <div v-if="$slots.footer" class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
            <slot name="footer"></slot>
        </div>
    </div>
</template>