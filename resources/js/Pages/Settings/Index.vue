<!-- resources/js/Pages/Settings/Index.vue -->
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Card from '@/Components/Shared/Card.vue';
import Button from '@/Components/Shared/Button.vue';

const props = defineProps({
    settings: Object,
    groups: Object
});

const activeGroup = ref('general');
const form = useForm({
    settings: Object.entries(props.settings).reduce((acc, [group, settings]) => {
        settings.forEach(setting => {
            acc.push({
                key: setting.key,
                value: setting.value,
                type: setting.type
            });
        });
        return acc;
    }, [])
});

const getSetting = (key) => {
    return form.settings.find(s => s.key === key);
};

const updateSetting = (key, value) => {
    const setting = form.settings.find(s => s.key === key);
    if (setting) {
        setting.value = value;
    }
};

const submit = () => {
    form.put(route('settings.update'));
};
</script>

<template>
    <AppLayout>
        <PageHeader title="System Settings" description="Configure system-wide settings and preferences" />

        <div class="flex space-x-6">
            <!-- Navigation -->
            <div class="w-64 flex-shrink-0">
                <div class="space-y-1">
                    <button v-for="(label, group) in groups" :key="group" @click="activeGroup = group" :class="[
                        'w-full text-left px-3 py-2 rounded-lg text-sm font-medium',
                        activeGroup === group
                            ? 'bg-primary-100 text-primary-700'
                            : 'text-gray-600 hover:bg-gray-50'
                    ]">
                        {{ label }}
                    </button>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="flex-1">
                <form @submit.prevent="submit">
                    <Card v-for="(label, group) in groups" :key="group" v-show="activeGroup === group">
                        <template #title>{{ label }}</template>

                        <div class="space-y-6">
                            <template v-for="setting in settings[group]" :key="setting.key">
                                <!-- String Input -->
                                <div v-if="setting.type === 'string'">
                                    <label :for="setting.key" class="block text-sm font-medium text-gray-700">
                                        {{ setting.description || setting.key }}
                                    </label>
                                    <input type="text" :id="setting.key" v-model="getSetting(setting.key).value"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                                </div>

                                <!-- Number Input -->
                                <div v-else-if="setting.type === 'number'">
                                    <label :for="setting.key" class="block text-sm font-medium text-gray-700">
                                        {{ setting.description || setting.key }}
                                    </label>
                                    <input type="number" :id="setting.key"
                                        v-model.number="getSetting(setting.key).value"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                                </div>

                                <!-- Text Area -->
                                <div v-else-if="setting.type === 'text'">
                                    <label :for="setting.key" class="block text-sm font-medium text-gray-700">
                                        {{ setting.description || setting.key }}
                                    </label>
                                    <textarea :id="setting.key" v-model="getSetting(setting.key).value" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                                </div>

                                <!-- Boolean Toggle -->
                                <div v-else-if="setting.type === 'boolean'" class="flex items-center justify-between">
                                    <label :for="setting.key" class="text-sm font-medium text-gray-700">
                                        {{ setting.description || setting.key }}
                                    </label>
                                    <button type="button" :class="[
                                        getSetting(setting.key).value ? 'bg-primary-600' : 'bg-gray-200',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                                    ]" @click="updateSetting(setting.key, !getSetting(setting.key).value)">
                                        <span :class="[
                                            getSetting(setting.key).value ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                        ]" />
                                    </button>
                                </div>
                            </template>
                        </div>

                        <template #footer>
                            <div class="flex justify-end">
                                <Button type="submit" variant="primary" :disabled="form.processing">
                                    Save Changes
                                </Button>
                            </div>
                        </template>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>