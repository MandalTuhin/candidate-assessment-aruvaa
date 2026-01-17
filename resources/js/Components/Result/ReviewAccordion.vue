<template>
    <div
        class="mb-6 sm:mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    >
        <button
            @click="reviewOpen = !reviewOpen"
            type="button"
            class="flex w-full cursor-pointer items-center justify-between border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 sm:p-6 text-left transition-all hover:from-blue-100 hover:to-indigo-100"
        >
            <div class="flex items-center gap-3">
                <svg
                    class="h-6 w-6 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                    ></path>
                </svg>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                        Review All Questions
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-gray-600">
                        View your answers and correct solutions
                    </p>
                </div>
            </div>
            <svg
                class="h-6 w-6 text-gray-600 transition-transform duration-200"
                :class="{ 'rotate-180': reviewOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                ></path>
            </svg>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform -translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="reviewOpen" class="bg-gray-50 p-4 sm:p-6">
                <div class="space-y-4 sm:space-y-6">
                    <div
                        v-for="question in questions"
                        :key="question.question_number"
                        class="overflow-hidden rounded-lg border-l-4 bg-white shadow-sm"
                        :class="{
                            'border-green-500': question.status === 'correct',
                            'border-red-500': question.status === 'incorrect',
                            'border-yellow-500': question.status === 'skipped',
                        }"
                    >
                        <div class="p-4 sm:p-6">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold"
                                            :class="{
                                                'bg-green-100 text-green-700':
                                                    question.status ===
                                                    'correct',
                                                'bg-red-100 text-red-700':
                                                    question.status ===
                                                    'incorrect',
                                                'bg-yellow-100 text-yellow-700':
                                                    question.status ===
                                                    'skipped',
                                            }"
                                        >
                                            {{ question.question_number }}
                                        </span>
                                        <span
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                                        >
                                            {{ question.language_name }}
                                        </span>
                                    </div>
                                    <p
                                        class="mt-2 text-sm sm:text-base font-semibold text-gray-900 break-words leading-relaxed"
                                    >
                                        {{ question.question_text }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <!-- Correct Icon -->
                                    <svg
                                        v-if="question.status === 'correct'"
                                        class="h-6 w-6 text-green-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <!-- Incorrect Icon -->
                                    <svg
                                        v-else-if="
                                            question.status === 'incorrect'
                                        "
                                        class="h-6 w-6 text-red-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <!-- Skipped Icon -->
                                    <svg
                                        v-else
                                        class="h-6 w-6 text-yellow-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <!-- Skipped Question -->
                                <div
                                    v-if="question.status === 'skipped'"
                                    class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 sm:p-4"
                                >
                                    <span
                                        class="text-xs sm:text-sm font-medium text-yellow-800"
                                    >
                                        <svg
                                            class="mr-1 inline h-4 w-4"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                        Question Skipped - No answer provided
                                    </span>
                                </div>

                                <!-- User Answer (for answered questions) -->
                                <div
                                    v-else
                                    class="rounded-lg p-3 sm:p-4"
                                    :class="{
                                        'bg-green-50 border border-green-200':
                                            question.status === 'correct',
                                        'bg-red-50 border border-red-200':
                                            question.status === 'incorrect',
                                    }"
                                >
                                    <span
                                        class="text-xs sm:text-sm font-medium"
                                        :class="{
                                            'text-green-700':
                                                question.status === 'correct',
                                            'text-red-700':
                                                question.status === 'incorrect',
                                        }"
                                    >
                                        Your Answer:
                                    </span>
                                    <span
                                        class="ml-2 text-sm sm:text-base font-semibold break-words"
                                        :class="{
                                            'text-green-800':
                                                question.status === 'correct',
                                            'text-red-800':
                                                question.status === 'incorrect',
                                        }"
                                    >
                                        {{ question.user_answer }}
                                    </span>
                                </div>

                                <!-- Correct Answer (for incorrect/skipped questions) -->
                                <div
                                    v-if="question.status !== 'correct'"
                                    class="rounded-lg border border-green-200 bg-green-50 p-3 sm:p-4"
                                >
                                    <span
                                        class="text-xs sm:text-sm font-medium text-green-700"
                                        >Correct Answer:</span
                                    >
                                    <span
                                        class="ml-2 text-sm sm:text-base font-semibold text-green-800 break-words"
                                    >
                                        {{ question.correct_answer }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref } from "vue";

defineProps({
    questions: {
        type: Array,
        required: true,
    },
});

const reviewOpen = ref(false);
</script>
