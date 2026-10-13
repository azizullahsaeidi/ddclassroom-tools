<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    permissions: Array,
    protectedRole: Boolean,
});

const groupedPermissions = computed(() => {
    return props.permissions.reduce((groups, permission) => {
        const group = permission.name.split('.')[0];
        (groups[group] ??= []).push(permission);
        return groups;
    }, {});
});
</script>

<template>
    <div class="space-y-8">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-900">Role name</label>
            <input id="name" v-model="form.name" :disabled="protectedRole" type="text" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" />
            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-gray-900">Permissions</label>
                <button v-if="!protectedRole" type="button" class="text-sm font-medium text-indigo-600" @click="form.permissions = permissions.map((permission) => permission.id)">Select all</button>
            </div>
            <div class="mt-3 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <fieldset v-for="(items, group) in groupedPermissions" :key="group" class="rounded-lg border p-4">
                    <legend class="px-1 text-sm font-semibold capitalize text-gray-900">{{ group.replaceAll('-', ' ') }}</legend>
                    <label v-for="permission in items" :key="permission.id" class="mt-2 flex items-start gap-2 text-sm text-gray-700">
                        <input v-model="form.permissions" :disabled="protectedRole" type="checkbox" :value="permission.id" class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                        <span>{{ permission.name }}</span>
                    </label>
                </fieldset>
            </div>
            <p v-if="form.errors.permissions" class="mt-2 text-sm text-red-600">{{ form.errors.permissions }}</p>
        </div>
    </div>
</template>
