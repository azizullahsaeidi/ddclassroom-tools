<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

defineProps({ roles: Array });
const page = usePage();
const can = (permission) => page.props.auth.user?.user_type_id === 1 || (page.props.auth.permissions ?? []).includes(permission);

const remove = (role) => {
    if (role.name !== 'Super Admin' && confirm(`Delete the ${role.name} role?`)) {
        router.delete(route('roles.destroy', role.id));
    }
};
</script>

<template>
    <Head title="Roles" />
    <AuthenticatedLayout>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Roles</h1>
                <p class="mt-1 text-sm text-gray-600">Group section permissions and assign these roles to users or teachers.</p>
            </div>
            <Link v-if="can('roles.create')" :href="route('roles.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create role</Link>
        </div>
        <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Assigned users</th>
                    <th class="px-6 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="role in roles" :key="role.id">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ role.name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ role.permissions_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ role.users_count }}</td>
                        <td class="space-x-3 px-6 py-4 text-right text-sm">
                            <Link v-if="can('roles.edit')" :href="route('roles.edit', role.id)" class="font-medium text-indigo-600 hover:text-indigo-900">Edit</Link>
                            <button v-if="can('roles.delete') && role.name !== 'Super Admin'" type="button" class="font-medium text-red-600 hover:text-red-900" @click="remove(role)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
