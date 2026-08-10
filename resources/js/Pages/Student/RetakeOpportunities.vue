<template>
    <Head title="Student Retake Opportunities" />
    <AuthenticatedLayout>
        <!-- Retake Opportunities List -->
        <div class="mb-2">

            <div class="space-y-9 divide-y divide-gray-200 mb-2">
                <div class="mt-6 grid xs:grid-cols-2 gap-y-8 sm:grid-cols-6 md:grid-cols-6 sm:gap-x-6 lg:grid-cols-10 xl:gap-x-8 mr-2">
                    <!-- Search -->
                    <div class="sm:col-span-2">
                        <div class="mt-1 rounded-md shadow-sm flex">
                            <input name="search" v-model="form.search" id="search" autocomplete="search"
                                placeholder="Search by student, email, or moodle_id"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300">
                        </div>
                    </div>
                    <!-- Sub Grade Filter -->
                    <div class="sm:col-span-2">
                        <div class="mt-1 rounded-md shadow-sm flex">
                            <select name="sub_grade_id" v-model="form.sub_grade_id"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300">
                                <option value="">All Grades</option>
                                <option v-for="grade in grades" :value="grade.id" :key="grade.id">{{ grade.full_name }}</option>
                            </select>
                        </div>
                    </div>
                    <!-- Year Filter -->
                    <div class="sm:col-span-2">
                        <div class="mt-1 rounded-md shadow-sm flex">
                            <select name="year_id" v-model="form.year_id"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300">
                                <option value="">All Years</option>
                                <option v-for="year in years" :value="year.id" :key="year.id">{{ year.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!-- Subject Filter -->
                    <div class="sm:col-span-2">
                        <div class="mt-1 rounded-md shadow-sm flex">
                            <select name="subject_id" v-model="form.subject_id"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300">
                                <option value="">All Subjects</option>
                                <option v-for="subject in subjects" :value="subject.id" :key="subject.id">{{ subject.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!-- Status Filter -->
                    <div class="sm:col-span-2">
                        <div class="mt-1 rounded-md shadow-sm flex">
                            <select name="is_passed" v-model="form.is_passed"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300">
                                <option value="">All Status</option>
                                <option value="1">Passed</option>
                                <option value="0">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="float-right mb-2 sm:col-span-2">
                        <button @click="exportReport"
                            class="rounded-md bg-green-600 px-3 py-2 text-[0.8125rem] font-semibold leading-5 text-white hover:bg-green-500">
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
            <!-- Retake Opportunities Table -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            NO
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Moodle ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Grade
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Year
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subject
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            First Chance Score
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Second Chance Score
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Third Chance Score
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Second Chance Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Third Chance Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(opportunity, index) in retakeOpportunities.data" :key="opportunity.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ index + 1 + (retakeOpportunities.current_page - 1) * retakeOpportunities.per_page }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.student?.fa_name || opportunity.student?.name }}
                            <span v-if="opportunity.student?.fa_father_name || opportunity.student?.father_name">
                                - {{ opportunity.student?.fa_father_name || opportunity.student?.father_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.student?.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.student?.id_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.sub_grade?.full_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.year?.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.subject?.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.score }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.second_chance_score }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.third_chance_score }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.second_chance_date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ opportunity.third_chance_date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="opportunity.is_passed ? 'text-green-600' : 'text-red-600'">
                                {{ opportunity.is_passed ? 'Passed' : 'Failed' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <no-record-found v-if="retakeOpportunities.data.length == 0"></no-record-found>
        <!-- Pagination -->
        <div v-if="retakeOpportunities.links.length > 3" class="mt-2">
            <div class="flex flex-wrap -mb-1">
                <template v-for="(link, p) in retakeOpportunities.links" :key="p">
                    <div v-if="link.url === null"
                        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                        v-html="link.label" />
                    <Link v-else
                        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                        :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import NoRecordFound from "../Partials/NoRecordFound.vue";
    import { router } from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import debounce from 'lodash.debounce';
    import { watch } from 'vue';

    const props = defineProps(['retakeOpportunities', 'grades', 'years', 'subjects', 'errors']);

    const form = reactive({
        search: '',
        sub_grade_id: '',
        year_id: '',
        subject_id: '',
        is_passed: ''
    });

    function exportReport() {
        const params = {
            search: form.search,
            sub_grade_id: form.sub_grade_id,
            year_id: form.year_id,
            subject_id: form.subject_id,
            is_passed: form.is_passed
        };

        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key];
            }
        });

        const queryString = new URLSearchParams(params).toString();
        window.location.href = `/students/retake-opportunities/export?${queryString}`;
    }

    watch(form, debounce(() => {
        router.get(route('students.retake-opportunities'), form, {
            preserveState: true,
        });
    }, 300));
</script>

