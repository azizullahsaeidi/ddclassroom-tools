<template>
    <Head title="Monthly Attendance Report" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Monthly Attendance Report
                    </h2>

                    <p class="text-sm text-gray-500">
                        Review monthly attendance before sending support
                        eligibility emails.
                    </p>
                </div>

                <div class="flex gap-2">
                    <button
                        type="button"
                        @click="generateAttendance"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                    >
                        Generate Attendance
                    </button>

                    <button
                        type="button"
                        @click="sendEmails"
                        :disabled="selectedIds.length === 0"
                        class="rounded-md px-4 py-2 text-sm font-semibold text-white"
                        :class="
                            selectedIds.length === 0
                                ? 'bg-gray-300 cursor-not-allowed'
                                : 'bg-green-600 hover:bg-green-500'
                        "
                    >
                        Send Email
                        <span v-if="selectedIds.length">
                            ({{ selectedIds.length }})
                        </span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-5">
                <!-- Year -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Year
                    </label>
                    <select
                        v-model="localFilters.year"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Years</option>
                        <option
                            v-for="year in years"
                            :key="year.id"
                            :value="year.name"
                        >
                            {{ year.name }}
                        </option>
                    </select>
                </div>

                <!-- Month -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Month
                    </label>
                    <select
                        v-model="localFilters.month_id"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Months</option>

                        <option
                            v-for="month in months"
                            :key="month.id"
                            :value="month.id"
                        >
                            {{ month.name }}
                        </option>
                    </select>
                </div>

                <!-- Grade -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Grade / Class
                    </label>
                    <select
                        v-model="localFilters.sub_grade_id"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Grades</option>

                        <option
                            v-for="subGrade in subGrades"
                            :key="subGrade.id"
                            :value="subGrade.id"
                        >
                            {{ subGrade.full_name }}
                        </option>
                    </select>
                </div>

                <!-- Absence Percentage -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Minimum Absence %
                    </label>
                    <input
                        v-model="localFilters.absence_percentage"
                        @change="getData"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Support Type -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Support Type
                    </label>
                    <select
                        v-model="localFilters.support_type"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Support Types</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button
                    type="button"
                    @click="resetFilters"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Reset Filters
                </button>
            </div>
        </div>

        <!-- Table -->
        <div
            class="min-w-full overflow-x-auto overflow-hidden rounded-lg shadow"
        >
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Checkbox -->
                        <th class="px-4 py-3 text-left">
                            <input
                                type="checkbox"
                                :checked="allSelectableSelected"
                                @change="toggleSelectAll"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                        </th>
                        <th :class="headerClass">Name</th>
                        <th :class="headerClass">Father's Name</th>
                        <th :class="headerClass">Phone Number</th>
                        <th :class="headerClass">Grade / Class</th>
                        <th :class="headerClass">Attendance</th>
                        <th :class="headerClass">Absence %</th>
                        <th :class="headerClass">Support Type</th>
                        <th :class="headerClass">Eligibility</th>
                        <th :class="headerClass">Email Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                        v-for="item in monthlyAttendanceLogs.data"
                        :key="item.id"
                    >
                        <td class="px-4 py-4">
                            <input
                                v-if="canSelect(item)"
                                v-model="selectedIds"
                                type="checkbox"
                                :value="item.id"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span v-else class="text-gray-300"> — </span>
                        </td>

                        <!-- Name -->
                        <td :class="cellClass">
                            {{ item.student?.name ?? "-" }}
                        </td>

                        <!-- Father -->
                        <td :class="cellClass">
                            {{ item.student?.father_name ?? "-" }}
                        </td>

                        <!-- Phone -->
                        <td :class="cellClass">
                            {{ item.student?.phone ?? "-" }}
                        </td>

                        <!-- Grade -->
                        <td :class="cellClass">
                            {{ item.sub_grade?.full_name ?? "-" }}
                        </td>

                        <!-- Attendance -->
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-700"
                        >
                            <div>
                                Total:
                                <strong>
                                    {{ item.total_hours }}
                                </strong>
                            </div>

                            <div class="text-green-600">
                                Present:
                                {{ item.total_presents }}
                            </div>

                            <div class="text-red-600">
                                Absent:
                                {{ item.total_absences }}
                            </div>
                        </td>

                        <!-- Absence -->
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <span
                                class="font-semibold"
                                :class="
                                    Number(item.absence_percentage) > 30
                                        ? 'text-red-600'
                                        : 'text-green-600'
                                "
                            >
                                {{
                                    Number(item.absence_percentage).toFixed(2)
                                }}%
                            </span>
                        </td>

                        <!-- Support -->
                        <td :class="cellClass">
                            {{ supportTypeLabel(item.support_type) }}
                        </td>

                        <!-- Eligibility -->
                        <td :class="cellClass">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                :class="
                                    item.is_eligible_for_support
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'
                                "
                            >
                                {{
                                    item.is_eligible_for_support ? "Yes" : "No"
                                }}
                            </span>
                        </td>

                        <!-- Email -->
                        <td :class="cellClass">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                :class="
                                    item.is_sent
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-700'
                                "
                            >
                                {{ item.is_sent ? "Sent" : "Not Sent" }}
                            </span>

                            <div
                                v-if="item.sent_at"
                                class="mt-1 text-xs text-gray-500"
                            >
                                {{ formatDate(item.sent_at) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <NoRecordFound v-if="monthlyAttendanceLogs.data.length === 0" />

        <!-- Pagination -->
        <div v-if="monthlyAttendanceLogs.links.length > 3" class="mt-3">
            <div class="-mb-1 flex flex-wrap">
                <template
                    v-for="(link, index) in monthlyAttendanceLogs.links"
                    :key="index"
                >
                    <div
                        v-if="link.url === null"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400"
                        v-html="link.label"
                    />

                    <Link
                        v-else
                        :href="link.url"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                        :class="{
                            'bg-blue-700 text-white': link.active,
                        }"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NoRecordFound from "./../Partials/NoRecordFound.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, reactive, ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    monthlyAttendanceLogs: Object,
    subGrades: Array,
    months: Array,
    years: Array,
    filters: Object,
});

const selectedIds = ref([]);

const localFilters = reactive({
    year: props.filters?.year || "",
    month_id: props.filters?.month_id || "",
    sub_grade_id: props.filters?.sub_grade_id || "",
    absence_percentage: props.filters?.absence_percentage ?? 30,
    support_type: props.filters?.support_type || "",
});

const headerClass =
    "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";

const cellClass = "px-6 py-4 whitespace-nowrap text-sm text-gray-700";

function getData() {
    selectedIds.value = [];

    router.get("/monthly-attendance-logs", localFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    localFilters.year = "";
    localFilters.month_id = "";
    localFilters.sub_grade_id = "";
    localFilters.absence_percentage = "";
    localFilters.support_type = "";

    getData();
}

function generateAttendance() {
    if (!localFilters.year || !localFilters.month_id) {
        Swal.fire(
            "Missing Information",
            "Please select Year and Month first.",
            "warning",
        );

        return;
    }

    Swal.fire({
        title: "Generate attendance?",
        text: "Existing monthly records will be updated.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Generate",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        router.post("/monthly-attendance-logs/generate",
            {
                year: localFilters.year,

                month_id: localFilters.month_id,

                sub_grade_id: localFilters.sub_grade_id || null,
            },
            {
                preserveScroll: true,

                onSuccess: () => {
                    selectedIds.value = [];

                    Swal.fire(
                        "Success",
                        "Monthly attendance generated successfully.",
                        "success",
                    );
                },
            },
        );
    });
}

function canSelect(item) {
    return !item.is_eligible_for_support && !item.is_sent;
}

const selectableIds = computed(() => {
    return props.monthlyAttendanceLogs.data
        .filter(canSelect)
        .map((item) => item.id);
});

const allSelectableSelected = computed(() => {
    if (selectableIds.value.length === 0) {
        return false;
    }

    return selectableIds.value.every((id) => selectedIds.value.includes(id));
});

function toggleSelectAll() {
    if (allSelectableSelected.value) {
        selectedIds.value = [];
        return;
    }

    selectedIds.value = [...selectableIds.value];
}

function sendEmails() {
    if (selectedIds.value.length === 0) {
        return;
    }

    Swal.fire({
        title: "Send attendance emails?",
        text:
            `${selectedIds.value.length} student(s) ` +
            "will be added to the email queue.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Send Email",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        router.post(
            "/monthly-attendance-logs/send-emails",
            {
                ids: selectedIds.value,
            },
            {
                preserveScroll: true,

                onSuccess: () => {
                    selectedIds.value = [];

                    Swal.fire(
                        "Queued",
                        "The selected emails were added to the queue.",
                        "success",
                    );
                },
            },
        );
    });
}

function supportTypeLabel(value) {
    if (value === "credit_card") {
        return "Credit Card";
    }

    if (value === "cash") {
        return "Cash";
    }

    return "-";
}

function formatDate(value) {
    if (!value) {
        return "";
    }

    return new Date(value).toLocaleString();
}
</script>
