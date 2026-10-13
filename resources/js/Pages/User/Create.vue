<template>
    <Head title="Create user" />
    <AuthenticatedLayout>
        <div class="py-4 bg-sky-30 rounded-lg border">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">

                    <!-- User List -->
                    <form @submit.prevent="submit">
                        <div class="space-y-12">

                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Create user</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-600">You can create a new user
                                    receive mail.
                                </p>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!-- Name -->
                                    <div class="sm:col-span-3">
                                        <label for="first-name"
                                            class="block text-sm font-medium leading-6 text-gray-900">First
                                            name</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.name" name="first-name" id="first-name"
                                                autocomplete="given-name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                <p class="mt-2 text-sm text-red-500" v-if="errors.name">{{ errors.name }}</p>
                                        </div>
                                    </div>

                                    <!-- Email address -->
                                    <div class="sm:col-span-3">
                                        <label for="email"
                                            class="block text-sm font-medium leading-6 text-gray-900">Email
                                            address</label>
                                        <div class="mt-2">
                                            <input id="email" v-model="form.email" name="email" type="email"
                                                autocomplete="email"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                <p class="mt-2 text-sm text-red-500" v-if="errors.email">{{ errors.email }}</p>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="sm:col-span-3">
                                        <label for="password"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Password</label>
                                        <div class="mt-2">
                                            <input type="password" v-model="form.password" name="password" id="password"
                                                autocomplete="password"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                <p class="mt-2 text-sm text-red-500" v-if="errors.password">{{ errors.password }}</p>
                                        </div>
                                    </div>

                                    <!-- Password confirm -->
                                    <div class="sm:col-span-3">
                                        <label for="password-confirmation"
                                            class="block text-sm font-medium leading-6 text-gray-900">Confirm
                                            </label>
                                        <div class="mt-2">
                                            <input id="password-confirmation" v-model="form.password_confirmation" name="password_confirmation" type="password"
                                                autocomplete="password-confirmation"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                        </div>
                                    </div>

                                    <!-- User Type  -->
                                    <div class="sm:col-span-3">
                                        <label for="user-type"
                                            class="block text-sm font-medium leading-6 text-gray-900">User Type
                                            </label>
                                        <div class="mt-2">
                                            <select id="user-type" v-model="form.user_type_id"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option value="" >...</option>
                                                <option v-for="userType in userTypes" :value="userType.id" :key="userType">{{ userType.name }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div v-if="canAssignRoles" class="sm:col-span-6">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Roles</label>
                                        <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            <label v-for="role in roles" :key="role.id" class="flex items-center gap-2 rounded-md border bg-white px-3 py-2 text-sm">
                                                <input v-model="form.roles" type="checkbox" :value="role.id" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                                {{ role.name }}
                                            </label>
                                        </div>
                                        <p class="mt-2 text-sm text-red-500" v-if="errors.roles">{{ errors.roles }}</p>
                                    </div>

                                    <!-- Is Active -->
                                    <div class="sm:col-span-2">
                                        <label for="postal-code"
                                            class="block text-sm font-medium leading-6 text-gray-900">Is Active
                                        </label>
                                        <div class="mt-2">
                                            <Switch @click="form.is_active = !form.is_active, isActive= true"
                                                :class="[form.is_active ? 'bg-indigo-600' : 'bg-gray-200',
                                                    'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
                                                ]">
                                                <span class="sr-only">Is Active</span>
                                                <span
                                                    :class="[form.is_active ? 'translate-x-5' : 'translate-x-0',
                                                        'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'
                                                    ]">
                                                    <span
                                                        :class="[form.is_active ? 'opacity-0 ease-out duration-100' :
                                                            'opacity-100 ease-in duration-200',
                                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
                                                        ]"
                                                        aria-hidden="true">
                                                        <svg class="h-3 w-3 text-gray-400" fill="none"
                                                            viewBox="0 0 12 12">
                                                            <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                    <span
                                                        :class="[form.is_active ? 'opacity-100 ease-in duration-200' :
                                                            'opacity-0 ease-out duration-100',
                                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
                                                        ]"
                                                        aria-hidden="true">
                                                        <svg class="h-3 w-3 text-indigo-600" fill="currentColor"
                                                            viewBox="0 0 12 12">
                                                            <path
                                                                d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                                        </svg>
                                                    </span>
                                                </span>
                                            </Switch>

                                        </div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="photo" class="block text-sm font-medium text-gray-700">
                                            Photo
                                        </label>
                                        <div
                                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="file-upload"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Upload a photo</span>
                                                        <input id="file-upload" @change="selectPhoto" name="file-upload"
                                                            type="file" class="sr-only" />
                                                    </label>
                                                    <p class="pl-1"></p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF up to 5MB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Pagination -->
    </AuthenticatedLayout>
</template>
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import {
        Inertia
    } from '@inertiajs/inertia';
    import {
        Switch
    } from '@headlessui/vue';
    import {
        computed,
        defineComponent,
        reactive,
        ref
    } from 'vue'
    import {
        Head,
        Link
    } from '@inertiajs/vue3';
    import { router } from '@inertiajs/vue3';

    const form = reactive({
        id: null,
        name: null,
        email: null,
        user_type_id: '',
        password: null,
        password_confirmation: null,
        is_active: true,
        photo: null,
        roles: [],
    })

    defineProps(['errors', 'userTypes', 'roles', 'canAssignRoles'])

    function submit() {
        router.post(route(`users.store`), form)
    }

    function selectPhoto($event) {
        form.photo = $event.target.files[0];
    }
</script>
