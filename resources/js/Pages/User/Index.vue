<template>

    <Head title="User List" />

    <AuthenticatedLayout>
        <!-- User List -->

        <Link v-if="can('users.create')" href="/users/create"
            class="pointer-events-auto mb-2 rounded-md bg-indigo-600 px-3 py-2 text-[0.8125rem] font-semibold leading-5 text-white hover:bg-indigo-500">
        Create
        </Link>
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">

            <!-- user list table -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User type
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Roles
                        </th>

                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users.data" :key="user.email">

                        <!-- user thumbnail -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img class="h-16 w-16 rounded-full" :src="user.photo ? user.photo : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'" lt="" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ user . name }}
                                    </div>

                                </div>
                            </div>
                        </td>


                        <!-- Email -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ user.email }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ user.user_type?.name }}
                        </td>

                        <!-- user state(Enabled or Disabled) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ user.is_active ? 'Active' : 'Disabled' }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span v-for="role in user.roles" :key="role.id" class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">
                                    {{ role.name }}
                                </span>
                            </div>
                        </td>

                        <!-- Edit -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link v-if="can('users.edit')" :href="'/users/' + user.id + '/edit'" class="text-indigo-600 hover:text-indigo-900">
                            Edit</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <no-record-found v-if="users.data.length == 0"></no-record-found>
        <!-- Pagination -->
        <pagination class="mt-2 mb-2" :links="users.links" />
    </AuthenticatedLayout>
</template>
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ErrorsAndMessages from "./../Partials/ErrorsAndMessages.vue";
    import NoRecordFound from "./../Partials/NoRecordFound.vue";
    import {
        computed,
        defineComponent,
        reactive
    } from 'vue'
    import {
        Head,
        Link,
        usePage
    } from '@inertiajs/vue3';

    defineProps(['users', 'errors']);
    const page = usePage();
    const can = (permission) => page.props.auth.user?.user_type_id === 1 || (page.props.auth.permissions ?? []).includes(permission);
</script>
