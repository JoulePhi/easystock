<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary'
    },
    size: {
        type: String,
        default: 'md'
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const classes = computed(() => {
    const baseClasses = 'inline-flex items-center rounded-md font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2';

    const variants = {
        primary: 'bg-primary-600 text-white dark:text-dark-500 hover:bg-primary-700 focus:ring-primary-500 dark:bg-dark-600 dark:hover:bg-dark-600/90 dark:focus:ring-dark-500',
        secondary: 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500'
    };

    const sizes = {
        sm: 'px-2.5 py-1.5 text-xs',
        md: 'px-4 py-2 text-sm',
        lg: 'px-6 py-3 text-base'
    };

    return `${baseClasses} ${variants[props.variant]} ${sizes[props.size]}`;
});
</script>

<template>
    <button :type="type" :disabled="disabled" :class="[classes, disabled && 'opacity-50 cursor-not-allowed']">
        <slot></slot>
    </button>
</template>