<!-- resources/js/Components/Sales/POSCartModal.vue -->
<script setup>
import { computed } from 'vue';
import Modal from '@/Components/Shared/Modal.vue';
import Button from '@/Components/Shared/Button.vue';
import { formatCurrency } from '@/Utils/format';

const props = defineProps({
    show: Boolean,
    form: Object,
    total: Number
});

const emit = defineEmits(['close', 'process']);

const change = computed(() => props.form.amount_paid - props.total);
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <!-- Modal content -->
    </Modal>
</template>