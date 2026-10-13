<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({ permissions: Array, sectionPermissions: Array });
const page = usePage();
const can = (permission) => page.props.auth.user?.user_type_id === 1 || (page.props.auth.permissions ?? []).includes(permission);
const isProtected = (permission) => props.sectionPermissions.includes(permission.name);
const remove = (permission) => {
    if (!isProtected(permission) && confirm(`Delete the ${permission.name} permission?`)) {
        router.delete(route('permissions.destroy', permission.id));
    }
};
</script>

<template>
    <Head title="Permissions" />
    <AuthenticatedLayout>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Permissions</h1>
                <p class="mt-1 text-sm text-gray-600">Section permissions are protected because routes and navigation depend on their names.</p>
            </div>
            <Link v-if="can('permissions.create')" :href="route('permissions.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create permission</Link>
        </div>
        <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Type</th>
                    <th class="px-6 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="permission in permissions" :key="permission.id">
                        <td class="px-6 py-4 font-mono text-sm text-gray-900">{{ permission.name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ permission.roles_count }}</td>
                        <td class="px-6 py-4"><span :class="isProtected(permission) ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700'" class="rounded-full px-2 py-1 text-xs font-medium">{{ isProtected(permission) ? 'Section' : 'Custom' }}</span></td>
                        <td class="space-x-3 px-6 py-4 text-right text-sm">
                            <Link v-if="can('permissions.edit') && !isProtected(permission)" :href="route('permissions.edit', permission.id)" class="font-medium text-indigo-600">Edit</Link>
                            <button v-if="can('permissions.delete') && !isProtected(permission)" class="font-medium text-red-600" @click="remove(permission)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
