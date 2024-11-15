<!-- resources/js/Pages/Inventory/Create.vue -->
<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/Shared/PageHeader.vue';
import Button from '@/Components/Shared/Button.vue';
import Card from '@/Components/Shared/Card.vue';
import { defineProps, computed, ref } from 'vue';

const props = defineProps({
    categories: Array,
    vendors: Array
});

const form = useForm({
    name: '',
    sku: '',
    category_id: '',
    vendor_id: '',
    unit: '',
    minimum_stock: 0,
    current_stock: 0,
    price: 0,
    cost: 0,
    description: '',
    image: null,
    image_url: null
});



const submit = () => {
    form.post(route('inventory.items.store'));
};

const fileInput = ref(null);
const handleDrop = (event) => {
    event.preventDefault();
    handleFileUpload(event.dataTransfer.files[0]);
};


const selectFile = () => {
    fileInput.value.click();
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = (e) => {
        form.image = file;
        form.image_url = e.target.result;
    };

    reader.readAsDataURL(file);
};

const previewUrl = computed(() => {
    return form.image_url;
});

</script>

<template>
    <AppLayout>
        <PageHeader title="Add New Item" description="Create a new inventory item">
            <template #actions>
                <Link :href="route('inventory.index')">
                <Button variant="secondary">
                    Cancel
                </Button>
                </Link>
            </template>
        </PageHeader>

        <Card>
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Item Information -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Name</label>
                        <input type="text" v-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Category</label>
                        <select v-model="form.category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.category_id }">
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id
                            }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Vendor</label>
                        <select v-model="form.vendor_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.vendor_id }">
                            <option value="">All Vendor</option>
                            <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                                {{ vendor.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.vendor_id" class="mt-1 text-sm text-red-600">{{ form.errors.vendor_id
                            }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">SKU</label>
                        <input type="text" v-model="form.sku"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.sku }" />
                        <p v-if="form.errors.sku" class="mt-1 text-sm text-red-600">{{ form.errors.sku }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Unit</label>
                        <input type="text" v-model="form.unit"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.unit }" />
                        <p v-if="form.errors.unit" class="mt-1 text-sm text-red-600">{{ form.errors.unit }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Minimum Stock</label>
                        <input type="text" v-model="form.minimum_stock"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.minimum_stock }" />
                        <p v-if="form.errors.minimum_stock" class="mt-1 text-sm text-red-600">{{
                            form.errors.minimum_stock }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Price</label>
                        <input type="text" v-model="form.price"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.price }" />
                        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Cost</label>
                        <input type="text" v-model="form.cost"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.cost }" />
                        <p v-if="form.errors.cost" class="mt-1 text-sm text-red-600">{{ form.errors.cost }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Description</label>
                        <textarea type="text" v-model="form.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-dark-500 dark:text-white dark:placeholder-dark-50 dark:border-dark-200 dark:focus:border-dark-600 dark:focus:ring-dark-600"
                            :class="{ 'border-red-500': form.errors.description }"></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description
                            }}</p>
                    </div>
                    <div class="col-span-2">
                        <div class="dropzone p-4 border-2 border-dashed rounded-lg cursor-pointer h-64"
                            @dragover.prevent @drop.prevent="handleDrop" @click="selectFile" :ref="fileInput">
                            <input type="file" ref="fileInput" accept="image/*" class="hidden"
                                @change="handleFileUpload" />
                            <div v-if="previewUrl" class="preview mt-4">
                                <img :src="previewUrl" alt="Image preview" class="h-60 rounded-lg object-fill mb-2" />
                            </div>
                            <div v-else class="text-center text-gray-500">
                                <p>Drag & drop or click to upload an image</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <Button type="button" variant="secondary" :disabled="form.processing"
                        @click="$inertia.visit(route('inventory.index'))">
                        Cancel
                    </Button>
                    <Button type="submit" variant="primary" :disabled="form.processing">
                        Create Item
                    </Button>
                </div>
            </form>
        </Card>
    </AppLayout>
</template>