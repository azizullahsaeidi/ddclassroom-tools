<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RoleForm from './Form.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ permissions: Array });
const form = useForm({ name: '', permissions: [] });
</script>

<template>
    <Head title="Create role" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl rounded-lg border bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-900">Create role</h1>
            <p class="mt-1 text-sm text-gray-600">Choose which project sections this role can access.</p>
            <form class="mt-8" @submit.prevent="form.post(route('roles.store'))">
                <RoleForm :form="form" :permissions="permissions" />
                <div class="mt-8 flex justify-end gap-3">
                    <Link :href="route('roles.index')" class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700">Cancel</Link>
                    <button :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Create role</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
