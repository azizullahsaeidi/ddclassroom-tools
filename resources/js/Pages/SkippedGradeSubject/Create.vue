<template>
    <Head title="Skipped Grade Subjects" />

    <AuthenticatedLayout>
        <div
            class="align-middle min-w-full p-2 bg-gray-50 overflow-x-auto shadow overflow-hidden sm:rounded-lg"
        >
            <div class="rounded-lg bg-white overflow-hidden shadow p-6">

                <h3
                    class="text-lg leading-6 font-medium text-gray-900 mb-2"
                >
                    Configure Skipped Subjects
                </h3>

                <p class="text-sm text-gray-500 mb-6">
                    Select only the subjects that are NOT included
                    for this class during the selected year.
                </p>

                <form
                    @submit.prevent="submit"
                    class="space-y-6"
                >

                    <!-- Sub Grade -->
                    <div>
                        <label
                            for="sub_grade_id"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Sub Grade
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="sub_grade_id"
                            v-model="form.sub_grade_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">
                                Select Sub Grade...
                            </option>

                            <option
                                v-for="subGrade in subGrades"
                                :key="subGrade.id"
                                :value="subGrade.id"
                            >
                                {{ subGrade.full_name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.sub_grade_id"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.sub_grade_id }}
                        </p>
                    </div>

                    <!-- Year -->
                    <div>
                        <label
                            for="year"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Year
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="year"
                            v-model="form.year"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">
                                Select Year...
                            </option>

                            <option
                                v-for="year in years"
                                :key="year.id"
                                :value="year.name"
                            >
                                {{ year.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.year"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.year }}
                        </p>
                    </div>

                    <!-- Subjects -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Subjects to Skip
                        </label>

                        <div
                            class="rounded-md border border-yellow-300 bg-yellow-50 p-3 mb-4"
                        >
                            <p class="text-sm text-yellow-800">
                                Check only subjects that this class
                                should NOT study or take an exam for.
                            </p>
                        </div>

                        <div
                            class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto"
                        >
                            <div
                                v-for="subject in subjects"
                                :key="subject.id"
                                class="flex items-center mb-3"
                            >
                                <input
                                    :id="`subject-${subject.id}`"
                                    v-model="form.subject_ids"
                                    type="checkbox"
                                    :value="subject.id"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                />

                                <label
                                    :for="`subject-${subject.id}`"
                                    class="ml-2 text-sm text-gray-700"
                                >
                                    {{ subject.name }}

                                    <span
                                        v-if="subject.en_name"
                                        class="text-gray-500"
                                    >
                                        ({{ subject.en_name }})
                                    </span>
                                </label>
                            </div>
                        </div>

                        <p
                            v-if="form.errors.subject_ids"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.subject_ids }}
                        </p>

                        <p class="mt-2 text-sm text-gray-500">
                            Subjects skipped:
                            <strong>
                                {{ form.subject_ids.length }}
                            </strong>
                        </p>
                    </div>

                    <!-- Note -->
                    <div>
                        <label
                            for="note"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Note / Reason
                        </label>

                        <textarea
                            id="note"
                            v-model="form.note"
                            rows="3"
                            maxlength="255"
                            placeholder="Optional reason for skipping these subjects..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />

                        <p
                            v-if="form.errors.note"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.note }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">

                        <Link
                            href="/skipped-grade-subjects"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white"
                            :class="
                                form.processing
                                    ? 'bg-indigo-300'
                                    : 'bg-indigo-600 hover:bg-indigo-700'
                            "
                        >
                            <span v-if="form.processing">
                                Saving...
                            </span>

                            <span v-else>
                                Save Configuration
                            </span>
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    subGrades: Array,
    subjects: Array,
    years: Array,
});

const form = useForm({
    sub_grade_id: '',
    year: '',
    subject_ids: [],
    note: '',
});

function submit() {
    form.post('/skipped-grade-subjects', {
        preserveScroll: true,

        onSuccess: () => {
            Swal.fire(
                'Success',
                'Skipped subjects saved successfully.',
                'success'
            );
        },
    });
}
</script>
