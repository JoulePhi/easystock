<!-- resources/js/Components/Reports/DateRangePicker.vue -->
<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { CalendarIcon } from '@heroicons/vue/24/outline';
import Flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

const props = defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: 'Select date range'
    }
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

watch(() => props.modelValue, (newValue) => {
    if (input.value && input.value._flatpickr) {
        input.value._flatpickr.setDate(newValue);
    }
});

onMounted(() => {
    if (input.value) {
        Flatpickr(input.value, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            onChange: (selectedDates, dateStr) => {
                emit('update:modelValue', dateStr);
            }
        });
    }
});

onBeforeUnmount(() => {
    if (input.value && input.value._flatpickr) {
        input.value._flatpickr.destroy();
    }
});
</script>

<template>
    <div class="relative">
        <input ref="input" type="text" :value="modelValue" :placeholder="placeholder" readonly
            class="w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500" />
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <CalendarIcon class="h-5 w-5 text-gray-400" />
        </div>
    </div>
</template>