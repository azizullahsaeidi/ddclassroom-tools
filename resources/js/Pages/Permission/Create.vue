<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
const form = useForm({ name: '' });
</script>

<template>
    <Head title="Create permission" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-xl rounded-lg border bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-900">Create permission</h1>
            <p class="mt-1 text-sm text-gray-600">Use a stable lowercase name such as records.export.</p>
            <form class="mt-8" @submit.prevent="form.post(route('permissions.store'))">
                <label for="name" class="block text-sm font-medium text-gray-900">Permission name</label>
                <input id="name" v-model="form.name" type="text" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                <div class="mt-8 flex justify-end gap-3">
                    <Link :href="route('permissions.index')" class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700">Cancel</Link>
                    <button :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Create permission</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
