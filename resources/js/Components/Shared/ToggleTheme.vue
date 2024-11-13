<script setup>
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'
import { ref, onMounted } from 'vue';

const isDark = ref(false)

onMounted(() => {
    const storedTheme = localStorage.getItem('theme')
    if (storedTheme) {
        isDark.value = storedTheme === 'dark'
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}
</script>

<template>
    <button @click="toggleTheme">
        <SunIcon v-if="isDark" class="h-10 w-10 text-grey-50 p-2 rounded-full bg-dark-300" />
        <MoonIcon v-if="!isDark" class="h-10 w-10 text-grey-900 p-2 rounded-full bg-grey-50" />
    </button>
</template>