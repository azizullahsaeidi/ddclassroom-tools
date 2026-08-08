<template>
    <Head title="Skipped Grade Subjects" />

    <AuthenticatedLayout>

        <!-- Top -->
        <div class="mb-4">

            <Link
                href="/skipped-grade-subjects/create"
                class="pointer-events-auto float-right mb-2 rounded-md bg-indigo-600 px-3 py-2 text-[0.8125rem] font-semibold leading-5 text-white hover:bg-indigo-500"
            >
                Configure Skipped Subjects
            </Link>

            <div class="clear-both"></div>

            <!-- Filters -->
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">

                <!-- Sub Grade -->
                <div>
                    <select
                        v-model="localFilters.sub_grade_id"
                        @change="getData"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                    >
                        <option value="">
                            All Sub Grades
                        </option>

                        <option
                            v-for="subGrade in subGrades"
                            :key="subGrade.id"
                            :value="subGrade.id"
                        >
                            {{ subGrade.full_name }}
                        </option>
                    </select>
                </div>

                <!-- Subject -->
                <div>
                    <select
                        v-model="localFilters.subject_id"
                        @change="getData"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                    >
                        <option value="">
                            All Subjects
                        </option>

                        <option
                            v-for="subject in subjects"
                            :key="subject.id"
                            :value="subject.id"
                        >
                            {{ subject.name }}
                        </option>
                    </select>
                </div>

                <!-- Year -->
                <div>
                    <select
                        v-model="localFilters.year"
                        @change="getData"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                    >
                        <option value="">
                            All Years
                        </option>

                        <option
                            v-for="year in years"
                            :key="year.id"
                            :value="year.name"
                        >
                            {{ year.name }}
                        </option>
                    </select>
                </div>

                <!-- Reset -->
                <div>
                    <button
                        type="button"
                        @click="resetFilters"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Reset Filters
                    </button>
                </div>

            </div>
        </div>

        <!-- Table -->
        <div
            class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg"
        >
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Sub Grade
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Skipped Subject
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Year
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Note
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Added By
                        </th>

                        <th class="relative px-6 py-3">
                            <span class="sr-only">
                                Actions
                            </span>
                        </th>

                    </tr>
                </thead>

                <tbody
                    class="bg-white divide-y divide-gray-200"
                >
                    <tr
                        v-for="item in skippedGradeSubjects.data"
                        :key="item.id"
                    >

                        <!-- Sub Grade -->
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            {{ item.sub_grade?.full_name }}
                        </td>

                        <!-- Subject -->
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm"
                        >
                            <span
                                class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700"
                            >
                                {{ item.subject?.name }}

                                <span v-if="item.subject?.en_name">
                                    &nbsp;({{ item.subject.en_name }})
                                </span>
                            </span>
                        </td>

                        <!-- Year -->
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            {{ item.year }}
                        </td>

                        <!-- Note -->
                        <td
                            class="px-6 py-4 text-sm text-gray-700"
                        >
                            {{ item.note }}
                        </td>

                        <!-- User -->
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                        >
                            {{ item.user?.name }}
                        </td>

                        <!-- Delete -->
                        <td
                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                        >
                        <Link
                            :href="`/skipped-grade-subjects/${item.id}/edit`"
                            class="text-indigo-600 hover:text-indigo-900"
                        >
                            Edit
                        </Link>
                            <span
                                class="text-red-700 hover:text-red-900 cursor-pointer"
                                @click="deleteItem(item.id)"
                            >
                                Delete
                            </span>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

        <NoRecordFound
            v-if="skippedGradeSubjects.data.length === 0"
        />

        <!-- Pagination -->
        <div
            v-if="skippedGradeSubjects.links.length > 3"
            class="mt-3"
        >
            <div class="flex flex-wrap -mb-1">

                <template
                    v-for="(link, index) in skippedGradeSubjects.links"
                    :key="index"
                >
                    <div
                        v-if="link.url === null"
                        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                        v-html="link.label"
                    />

                    <Link
                        v-else
                        :href="link.url"
                        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                        :class="{
                            'bg-blue-700 text-white': link.active
                        }"
                        v-html="link.label"
                    />
                </template>

            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NoRecordFound from './../Partials/NoRecordFound.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    skippedGradeSubjects: Object,
    subGrades: Array,
    subjects: Array,
    years: Array,
    filters: Object,
});

const localFilters = reactive({
    sub_grade_id: props.filters?.sub_grade_id || '',
    subject_id: props.filters?.subject_id || '',
    year: props.filters?.year || '',
});

function getData() {
    router.get(
        '/skipped-grade-subjects',
        localFilters,
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

function resetFilters() {
    localFilters.sub_grade_id = '';
    localFilters.subject_id = '';
    localFilters.year = '';

    getData();
}

function deleteItem(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This subject will no longer be skipped.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel',
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        router.delete(
            `/skipped-grade-subjects/${id}`,
            {
                preserveScroll: true,

                onSuccess: () => {
                    Swal.fire(
                        'Removed',
                        'Skipped subject removed successfully.',
                        'success'
                    );
                },
            }
        );
    });
}
</script>
