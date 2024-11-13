<!-- resources/js/Pages/Users/Create.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import UserForm from '@/Components/Users/UserForm.vue';

defineProps({
    roles: Array
});
</script>

<template>
    <AppLayout>
        <PageHeader title="Create User" description="Create a new system user" />

        <Card>
            <UserForm :roles="roles" />
        </Card>
    </AppLayout>
</template>