<template>
    <Head title="Create Student" />
    <AuthenticatedLayout>
        <div class="py-4 bg-sky-30 rounded-lg border">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Create Student -->
                    <form @submit.prevent="submit">
                        <div class="space-y-12">
                            <div class="border-b border-gray-900/10 pb-12">
                                <h2
                                    class="text-base font-semibold leading-7 text-gray-900"
                                >
                                    Create Score
                                </h2>
                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    You can create student's scores.
                                </p>

                                <div
                                    class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6"
                                >
                                    <!-- Grade -->
                                    <div class="sm:col-span-1">
                                        <label
                                            for="sub_grade_id"
                                            class="block text-sm font-medium leading-6 text-gray-900"
                                            >Grade
                                        </label>
                                        <div class="mt-2">
                                            <select
                                                v-model="form.sub_grade_id"
                                                @change="getStudents()"
                                                name="grade"
                                                id="grade"
                                                autocomplete="grade"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            >
                                                <option value="">...</option>
                                                <option
                                                    v-for="grade in grades"
                                                    :value="grade.id"
                                                    :key="grade"
                                                >
                                                    {{ grade.full_name }}
                                                </option>
                                            </select>
                                            <p
                                                class="mt-2 text-sm text-red-500"
                                                v-if="errors.sub_grade_id"
                                            >
                                                {{ errors.sub_grade_id }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-1">
                                        <label
                                            for="subject_id"
                                            class="block text-sm font-medium leading-6 text-gray-900"
                                            >Subject
                                        </label>
                                        <div class="mt-2">
                                            <select
                                                v-model="form.subject_id"
                                                @change="getStudents()"
                                                name="subject"
                                                id="subject"
                                                autocomplete="subject"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            >
                                                <option value="">...</option>
                                                <option
                                                    v-for="subject in subjects"
                                                    :value="subject.id"
                                                    :key="subject"
                                                >
                                                    {{ subject.name }}
                                                </option>
                                            </select>
                                            <p
                                                class="mt-2 text-sm text-red-500"
                                                v-if="errors.subject_id"
                                            >
                                                {{ errors.subject_id }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-1">
                                        <label
                                            for="teacher"
                                            class="block text-sm font-medium leading-6 text-gray-900"
                                            >Teacher
                                        </label>
                                        <div class="mt-2">
                                            <select
                                                v-model="form.teacher_id"
                                                name="teacher"
                                                id="teacher"
                                                autocomplete="teacher"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            >
                                                <option value="">...</option>
                                                <option
                                                    v-for="teacher in teachers"
                                                    :value="teacher.id"
                                                    :key="teacher"
                                                >
                                                    {{ teacher.name }}
                                                </option>
                                            </select>
                                            <p
                                                class="mt-2 text-sm text-red-500"
                                                v-if="errors.teacher_id"
                                            >
                                                {{ errors.teacher_id }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Year -->
                                    <div class="sm:col-span-1">
                                        <label
                                            for="year"
                                            class="block text-sm font-medium leading-6 text-gray-900"
                                            >Year
                                        </label>
                                        <div class="mt-2">
                                            <select
                                                v-model="form.year"
                                                @change="getStudents()"
                                                name="year"
                                                id="year"
                                                autocomplete="year"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            >
                                                <option value="">...</option>
                                                <option
                                                    v-for="year in years"
                                                    :value="year.name"
                                                    :key="year"
                                                >
                                                    {{ year.name }}
                                                </option>
                                            </select>
                                            <p
                                                class="mt-2 text-sm text-red-500"
                                                v-if="errors.year"
                                            >
                                                {{ errors.year }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label
                                            for="subject_id"
                                            class="block text-sm font-medium leading-6 text-gray-900"
                                            >Exam Type
                                        </label>
                                        <div class="mt-2">
                                            <select
                                                name="name"
                                                @change="getStudents()"
                                                v-model="form.type_id"
                                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-md sm:text-sm border-gray-300"
                                            >
                                                <option value="">
                                                    Select Exam
                                                </option>
                                                <option
                                                    v-for="type in types"
                                                    :value="type.id"
                                                    :key="type"
                                                >
                                                    {{ type.name }}
                                                </option>
                                            </select>
                                            <p
                                                class="mt-2 text-sm text-red-500"
                                                v-if="errors.type"
                                            >
                                                {{ errors.type }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <div
                                            class="mt-1 rounded-md shadow-sm flex"
                                        ></div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

                                                <div class="relative">
                                                    <div v-if="studentScores.length > 0" class="flex justify-end mb-4 mt-4 md:mt-6">
                                                        <button
                                                            type="submit"
                                                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        >
                                                            Save
                                                        </button>
                                                    </div>
                                                    <table class="min-w-full table-fixed divide-y divide-gray-200">
                                                        <thead class="bg-sky-100">
                                                            <tr>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                Result
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                Total Score
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                Total
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                                                >
                                                                Evaluation
                                                                {{  selectedScores.length }}
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                پریزنتیشن
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                امتحانات صنفی و ارزیابی
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                    فعالیت صنفی و حاضری
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                کارخانگی
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                    امتحان تحریری و امتحان تقریری
                                                                </th>

                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                    Father Name
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                    Name
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                Moodle ID
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                                >
                                                                NO
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                            <tr
                                                                v-for="(studentScore, index) in studentScores"
                                                                :key="index"
                                                                class="bg-yellow-50/20"
                                                                :class="[selectedScores.includes(studentScore?.id) && '!bg-green-100']"
                                                            >
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900"
                                                                :class="[(studentScore.totalAmount != studentScore.total) && 'bg-red-600']">
                                                                    {{  studentScore.totalAmount - studentScore.total }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    {{ studentScore.totalAmount }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        id="total"
                                                                        v-model="studentScore.total"
                                                                        readonly
                                                                        name="total"
                                                                        type="number"
                                                                        step="0.01"
                                                                        :max="scoreLimits?.total"
                                                                        autocomplete="total"
                                                                        :placeholder="'0 - '+ scoreLimits?.total"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        id="evaluation"
                                                                        v-model="studentScore.evaluation"
                                                                        name="evaluation"
                                                                        type="number"
                                                                        step="0.01"
                                                                        :max="scoreLimits?.evaluation"
                                                                        autocomplete="evaluation"
                                                                        :placeholder="'0 - '+ scoreLimits?.evaluation"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        @input="
                                                                            updateStudentScores(
                                                                                $event,
                                                                                index
                                                                            )
                                                                        "
                                                                        id="homework"
                                                                        v-model="studentScore.homework"
                                                                        name="homework"
                                                                        type="number"
                                                                        autocomplete="homework"
                                                                        :placeholder="'0 - '+ scoreLimits.homework"
                                                                        step="0.01"
                                                                        :max="scoreLimits.homework"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        @input="
                                                                            updateStudentScores(
                                                                                $event,
                                                                                index
                                                                            )
                                                                        "
                                                                        id="activity"
                                                                        v-model="studentScore.activity"
                                                                        name="activity"
                                                                        type="number"
                                                                        autocomplete="activity"
                                                                        :placeholder="'0 - '+ scoreLimits.activity"
                                                                        step="0.01"
                                                                        :max="scoreLimits.activity"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        @input="
                                                                            updateStudentScores(
                                                                                $event,
                                                                                index
                                                                            )
                                                                        "
                                                                        id="attendance"
                                                                        v-model="studentScore.attendance"
                                                                        name="attendance"
                                                                        type="number"
                                                                        autocomplete="attendance"
                                                                        :placeholder="'0 - '+ scoreLimits.attendance"
                                                                        step="0.01"
                                                                        :max="scoreLimits.attendance"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        @input="
                                                                            updateStudentScores(
                                                                                $event,
                                                                                index
                                                                            )
                                                                        "
                                                                        id="oral"
                                                                        v-model="studentScore.oral"
                                                                        name="oral"
                                                                        type="number"
                                                                        autocomplete="oral"
                                                                        :placeholder="'0 - '+ scoreLimits.oral"
                                                                        step="0.01"
                                                                        :max="scoreLimits.oral"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    <input
                                                                        @input="
                                                                            updateStudentScores(
                                                                                $event,
                                                                                index
                                                                            )
                                                                        "
                                                                        id="written"
                                                                        v-model="studentScore.written"
                                                                        name="written"
                                                                        type="number"
                                                                        autocomplete="written"
                                                                        :placeholder="'0 - '+ scoreLimits.written"
                                                                        step="0.01"
                                                                        :max="scoreLimits.written"
                                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                                    />
                                                                </td>


                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    {{ studentScore?.name }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    {{ studentScore?.father_name }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    {{ studentScore?.moodle_id }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                                    {{ studentScore?.no }}
                                                                </td>
                                                            </tr>
                                                        </tbody>

                                                    </table>

                                                    <div
                                                        v-if="!studentScores.length"
                                                        class="py-24 px-8 flex flex-col gap-4 justify-center items-center text-center"
                                                    >
                                                        <h3 class="text-base md:text-lg max-w-lg text-gray-600">
                                                            Please copy the score list from Google Sheet, then
                                                            <br>
                                                            press
                                                            <kbd
                                                                class="inline-flex items-center rounded border border-gray-200 px-1 font-sans text-gray-400"
                                                                >Ctrl+V</kbd
                                                            >
                                                        </h3>
                                                        <div class="flex mb-4 md:mb-6">
                                                            <a
                                                                class="inline-flex items-center justify-center rounded-lg border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto disabled:opacity-80 disabled:cursor-not-allowed primary px-5 py-2.5 text-sm sm:text-base font-medium"
                                                                href="/assets/samples/student-score-list.xlsx"
                                                            >
                                                                Download score list format
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="mt-6 flex items-center justify-end gap-x-6" v-if="studentScores.length > 0">
                            <button
                                type="button"
                                @click="studentScores = []"
                                class="text-sm font-semibold leading-6 text-gray-900"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Pagination -->
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { reactive, ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

const props = defineProps([
    "grades",
    "errors",
    "types",
    "years",
    "teachers",
    "subjects",
    "midtermScores",
    "finalScores",
]);

const form = reactive({
    sub_grade_id: "",
    teacher_id: "",
    year: "",
    subject_id: "",
    type_id: "",
});

const scoreLimits = ref([]);



function submit() {
    router.post(route(`student-score.store`), {
                    studentScores: JSON.stringify(studentScores.value),
                    ...form,
                }, {
        forceFormData: true,
        onFinish: (res) => {
            studentScores.value = [];
            Swal.fire(`Created`, `Students have been successfully created.`);
        },
    });
}

function getStudents() {
    if (form.year && form.subject_id && form.sub_grade_id && form.type_id) {
        scoreLimits.value = form.type_id == 1 ? props.midtermScores: props.finalScores;
    }else{
        studentScores.value = [];
    }
}

const selectedScores = ref([]);


function validateInput(event, index) {
    const inputValue = event.target.value.trim();
    const inputName = event.target.name.trim();

    const scores = form.type_id === 1 ? props.midtermScores : props.finalScores;
    const max = scores[inputName];

    if (inputValue !== "" && (isNaN(inputValue) || inputValue > max)) {
        alert(`Please enter a number between ${0} and ${max}.`);
        form[inputName][index] = "";

        return false;
    }
    return true;
}

function updateStudentScores(event, index) {
    if (!validateInput(event, index)) return;

    form.total[index] = Number(form.written[index] || 0) + Number(form.oral[index] || 0) + Number(form.attendance[index] || 0) + Number(form.evaluation[index] || 0) + Number(form.homework[index] || 0) + Number(form.activity[index] || 0);
}



const studentScores = ref([]);
document.addEventListener('paste', event => {
    const data = getClipboardData(event);
    mapstudentScores(data);
});

function getClipboardData(event) {
    return event.clipboardData.getData('text');
}

function mapstudentScores(data) {
    if (form.year && form.subject_id && form.sub_grade_id && form.type_id) {
        const rows = data.split('\n').filter((row, i) => i != 0 && row !== '');
        if (rows.length > 40) {
            alert('Sorry! you are not able to add more then 40 students.');
            return;
        }

        studentScores.value = rows.map((row, index) => {
            const cols = row.split('\t');
            return {
                total: Number(cols[0] || 0),
                evaluation: Number(cols[1] || 0),
                homework: Number(cols[2] || 0),
                activity: Number(cols[3] || 0),
                attendance: Number(cols[4] || 0),
                oral: Number(cols[5] || 0),
                written: Number(cols[6] || 0),
                name: cols[7],
                father_name: cols[8],
                moodle_id: cols[9],
                no: cols[10],
                totalAmount: Number(cols[1] || 0) + Number(cols[2] || 0) + Number(cols[3] || 0) + Number(cols[4] || 0) + Number(cols[5] || 0) + Number(cols[6] || 0),
            };
        });
    }else{
        alert('Please select all above fields')
    }
}
</script>
