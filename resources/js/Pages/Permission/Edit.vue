<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
const props = defineProps({ permission: Object, isProtected: Boolean });
const form = useForm({ name: props.permission.name });
</script>

<template>
    <Head title="Edit permission" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-xl rounded-lg border bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-900">Edit permission</h1>
            <form class="mt-8" @submit.prevent="form.put(route('permissions.update', permission.id))">
                <label for="name" class="block text-sm font-medium text-gray-900">Permission name</label>
                <input id="name" v-model="form.name" :disabled="isProtected" type="text" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" />
                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                <div class="mt-8 flex justify-end gap-3">
                    <Link :href="route('permissions.index')" class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700">Cancel</Link>
                    <button :disabled="form.processing || isProtected" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Save changes</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
