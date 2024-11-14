<script setup>
</script>


<template>
    <div>
        <label class="block text-sm font-medium text-gray-700">Expected Date</label>
        <input type="date" v-model="form.expected_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            :class="{ 'border-red-500': form.errors.expected_date }" />
        <p v-if="form.errors.expected_date" class="mt-1 text-sm text-red-600">
            {{ form.errors.expected_date }}
        </p>
    </div>
</template>