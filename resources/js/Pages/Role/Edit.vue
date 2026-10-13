<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RoleForm from './Form.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ role: Object, permissions: Array, isProtected: Boolean });
const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((permission) => permission.id),
});
</script>

<template>
    <Head title="Edit role" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl rounded-lg border bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-900">Edit role</h1>
            <p v-if="isProtected" class="mt-2 rounded-md bg-amber-50 p-3 text-sm text-amber-800">The Super Admin role always retains every permission.</p>
            <form class="mt-8" @submit.prevent="form.put(route('roles.update', role.id))">
                <RoleForm :form="form" :permissions="permissions" :protected-role="isProtected" />
                <div class="mt-8 flex justify-end gap-3">
                    <Link :href="route('roles.index')" class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700">Cancel</Link>
                    <button :disabled="form.processing || isProtected" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Save changes</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
