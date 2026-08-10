<template>

    <Head title="Edit student" />
    <AuthenticatedLayout>

        <!-- student list btn -->
        <div class="my-2">
            <Link href="/student-score"
                class="pointer-events-auto  mb-2 rounded-md bg-indigo-600 px-3 py-2 text-[0.8125rem] font-semibold leading-5 text-white hover:bg-indigo-500">
            Student Score
            </Link>
        </div>
        <div class="py-4 bg-sky-30 rounded-lg border">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Update Student -->
                    <form @submit.prevent="submit">
                        <div class="space-y-12">

                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Edit Score</h2>
                                <p v-if="score" class="mt-1 text-sm leading-6 text-gray-600">
                                    {{ score . student . name }} - {{ score . student . father_name }}
                                    - {{ score . sub_grade . name }} - {{ score . type == 1 ? 'چهارنیم ماهه' : 'سالانه' }}
                                </p>

                                <!-- Retake Chance Selection -->
                                <div class="mt-6 sm:col-span-3">
                                    <label for="chance" class="block text-sm font-medium leading-6 text-gray-900">
                                        Chance</label>
                                    <div class="mt-2">
                                        <select id="chance" v-model="form.chance" name="chance"
                                            :disabled="props.selectedChance === 'third'"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <option value="first" :disabled="props.selectedChance === 'second' || props.selectedChance === 'third'">...</option>
                                            <option value="second" :disabled="props.selectedChance === 'third'">Second Chance</option>
                                            <option value="third">Third Chance</option>
                                        </select>
                                        <p class="mt-2 text-sm text-red-500" v-if="errors.chance">
                                            {{ errors . chance }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!-- Written -->
                                    <div class="sm:col-span-3">
                                        <label for="written" class="block text-sm font-medium leading-6 text-gray-900">
                                            Written</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.written" name="written" id="written"
                                                autocomplete="given-name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.written">
                                                {{ errors . written }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Verbal -->
                                    <div class="sm:col-span-3">
                                        <label for="verbal" class="block text-sm font-medium leading-6 text-gray-900">
                                            Verbal</label>
                                        <div class="mt-2">
                                            <input id="verbal" v-model="form.verbal" name="verbal" type="text"
                                                autocomplete="verbal"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.verbal">
                                                {{ errors . verbal }}</p>
                                        </div>
                                    </div>

                                    <!-- Attendance -->
                                    <div class="sm:col-span-3">
                                        <label for="attendance"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Attendance</label>
                                        <div class="mt-2">
                                            <input id="attendance" v-model="form.attendance" name="attendance"
                                                type="text" autocomplete="attendance"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.attendance">
                                                {{ errors . attendance }}</p>
                                        </div>
                                    </div>

                                    <!-- Activity -->
                                    <div class="sm:col-span-3">
                                        <label for="activity" class="block text-sm font-medium leading-6 text-gray-900">
                                            Activity</label>
                                        <div class="mt-2">
                                            <input id="activity" v-model="form.activity" name="activity" type="text"
                                                autocomplete="activity"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.activity">
                                                {{ errors . activity }}</p>
                                        </div>
                                    </div>

                                    <!-- Evaluation -->
                                    <div class="sm:col-span-3">
                                        <label for="evaluation"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Evaluation</label>
                                        <div class="mt-2">
                                            <input id="evaluation" v-model="form.evaluation" name="evaluation"
                                                type="text" autocomplete="evaluation"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.evaluation">
                                                {{ errors . evaluation }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Homework -->
                                    <div class="sm:col-span-3">
                                        <label for="homework"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Homework</label>
                                        <div class="mt-2">
                                            <input id="homework" v-model="form.homework" name="homework"
                                                type="text" autocomplete="homework"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.homework">
                                                {{ errors . homework }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="sm:col-span-3">
                                        <label for="total" class="block text-sm font-medium leading-6 text-gray-900">
                                            Total</label>
                                        <div class="mt-2">
                                            <input id="total" v-model="form.total" name="total" type="text"
                                                autocomplete="total"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.total">
                                                {{ errors . total }}</p>
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
    </AuthenticatedLayout>
</template>
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ErrorsAndMessages from "../Partials/ErrorsAndMessages.vue";
    import NoRecordFound from "../Partials/NoRecordFound.vue";
    import Swal from 'sweetalert2'
    import debounce from 'lodash.debounce'
    import {
        watch
    } from 'vue';

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
    import {
        router
    } from '@inertiajs/vue3';

    const props = defineProps(['score', 'errors', 'retakeOpportunity', 'selectedChance']);

    const form = reactive({
        id: props.score.id,
        total: props.score.total ? props.score.total : 0,
        written: props.score.written ? props.score.written : 0,
        activity: props.score.activity ? props.score.activity : 0,
        verbal: props.score.verbal ? props.score.verbal : 0,
        attendance: props.score.attendance ? props.score.attendance : 0,
        homework: props.score.homework ? props.score.homework : 0,
        evaluation: props.score.evaluation ? props.score.evaluation : 0,
        type: props.score.type,
        chance: props.selectedChance || 'first',
        _method: 'put'
    })

    function submit() {
        router.post(route(`student-score.update`, {
            'id': form.id
        }), form)
    }


    watch([() => form.homework, () => form.activity, () => form.attendance, () => form.evaluation, () => form.written, () => form.verbal], () => {
        form.total = calculateTotalScore(form.homework, form.evaluation, form.written, form.verbal, form.attendance, form.activity);
    });

    watch(() => form.chance, (newChance) => {
        const currentChance = props.selectedChance || 'first';

        if (currentChance === 'third' && newChance !== 'third') {
            form.chance = 'third';
            return;
        }

        if (currentChance === 'second' && newChance === 'first') {
            form.chance = 'second';
            return;
        }
    });

    function calculateTotalScore(homework, evaluation, written, verbal, attendance, activity) {
        // Perform the necessary calculations here
        return Number(homework) + Number(evaluation) + Number(written) + Number(verbal) + Number(attendance) + Number(activity);
    }
</script>
