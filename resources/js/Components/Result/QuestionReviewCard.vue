<template>
    <div
        class="overflow-hidden rounded-lg border-l-4 bg-white shadow-sm"
        :class="borderColorClass"
    >
        <div class="p-4 sm:p-6">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex-1">
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold"
                            :class="numberBadgeClass"
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
                    <component :is="statusIcon" :class="statusIconClass" />
                </div>
            </div>

            <div class="space-y-3">
                <!-- Skipped Question -->
                <div
                    v-if="question.status === 'skipped'"
                    class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 sm:p-4"
                >
                    <span class="text-xs sm:text-sm font-medium text-yellow-800">
                        <svg
                            class="mr-1 inline h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Question Skipped - No answer provided
                    </span>
                </div>

                <!-- User Answer (for answered questions) -->
                <div
                    v-else
                    class="rounded-lg p-3 sm:p-4"
                    :class="userAnswerClass"
                >
                    <span class="text-xs sm:text-sm font-medium" :class="userAnswerLabelClass">
                        Your Answer:
                    </span>
                    <span
                        class="ml-2 text-sm sm:text-base font-semibold break-words"
                        :class="userAnswerTextClass"
                    >
                        {{ question.user_answer }}
                    </span>
                </div>

                <!-- Correct Answer (for incorrect/skipped questions) -->
                <div
                    v-if="question.status !== 'correct'"
                    class="rounded-lg border border-green-200 bg-green-50 p-3 sm:p-4"
                >
                    <span class="text-xs sm:text-sm font-medium text-green-700">
                        Correct Answer:
                    </span>
                    <span class="ml-2 text-sm sm:text-base font-semibold text-green-800 break-words">
                        {{ question.correct_answer }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, h } from 'vue';

const props = defineProps({
    question: {
        type: Object,
        required: true,
    },
});

const borderColorClass = computed(() => ({
    'border-green-500': props.question.status === 'correct',
    'border-red-500': props.question.status === 'incorrect',
    'border-yellow-500': props.question.status === 'skipped',
}));

const numberBadgeClass = computed(() => ({
    'bg-green-100 text-green-700': props.question.status === 'correct',
    'bg-red-100 text-red-700': props.question.status === 'incorrect',
    'bg-yellow-100 text-yellow-700': props.question.status === 'skipped',
}));

const userAnswerClass = computed(() => ({
    'bg-green-50 border border-green-200': props.question.status === 'correct',
    'bg-red-50 border border-red-200': props.question.status === 'incorrect',
}));

const userAnswerLabelClass = computed(() => ({
    'text-green-700': props.question.status === 'correct',
    'text-red-700': props.question.status === 'incorrect',
}));

const userAnswerTextClass = computed(() => ({
    'text-green-800': props.question.status === 'correct',
    'text-red-800': props.question.status === 'incorrect',
}));

const statusIcon = computed(() => {
    const icons = {
        correct: () => h('svg', {
            class: 'h-6 w-6',
            fill: 'currentColor',
            viewBox: '0 0 20 20'
        }, [
            h('path', {
                'fill-rule': 'evenodd',
                d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
                'clip-rule': 'evenodd'
            })
        ]),
        incorrect: () => h('svg', {
            class: 'h-6 w-6',
            fill: 'currentColor',
            viewBox: '0 0 20 20'
        }, [
            h('path', {
                'fill-rule': 'evenodd',
                d: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
                'clip-rule': 'evenodd'
            })
        ]),
        skipped: () => h('svg', {
            class: 'h-6 w-6',
            fill: 'currentColor',
            viewBox: '0 0 20 20'
        }, [
            h('path', {
                'fill-rule': 'evenodd',
                d: 'M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z',
                'clip-rule': 'evenodd'
            })
        ])
    };
    
    return icons[props.question.status] || icons.skipped;
});

const statusIconClass = computed(() => ({
    'text-green-600': props.question.status === 'correct',
    'text-red-600': props.question.status === 'incorrect',
    'text-yellow-600': props.question.status === 'skipped',
}));
</script>