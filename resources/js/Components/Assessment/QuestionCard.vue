<template>
    <article class="mb-4 sm:mb-6 lg:mb-8 rounded-lg border-l-4 border-blue-500 bg-gray-50 p-3 sm:p-4 lg:p-6">
        <header class="mb-3 sm:mb-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-4">
                <h2 class="text-sm sm:text-base lg:text-lg xl:text-xl font-bold text-gray-900 break-words leading-tight">
                    <span class="text-blue-600">Q{{ questionNumber }}:</span>
                    <span class="ml-1 sm:ml-2">{{ question.question_text }}</span>
                </h2>
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 shrink-0">
                    {{ question.language_name }}
                </span>
            </div>
        </header>

        <fieldset class="space-y-2 sm:space-y-3 lg:space-y-4" :aria-labelledby="`question-${question.id}-legend`">
            <legend :id="`question-${question.id}-legend`" class="sr-only">
                Question {{ questionNumber }}: {{ question.question_text }}
            </legend>
            
            <div
                v-for="(option, optIndex) in question.options"
                :key="optIndex"
                class="relative w-full"
            >
                <label
                    class="flex cursor-pointer items-start gap-2 sm:gap-3 rounded-lg border p-2 sm:p-3 lg:p-4 transition-all hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-200 has-[:checked]:ring-1 has-[:checked]:ring-blue-600 w-full"
                >
                    <input
                        type="radio"
                        :name="`current_answer_${question.id}`"
                        :value="option"
                        :checked="question.selectedAnswer === option"
                        @change="$emit('select', option)"
                        class="mt-0.5 sm:mt-1 h-4 w-4 shrink-0 border-gray-300 text-blue-600 focus:ring-blue-600"
                        :aria-describedby="`option-${question.id}-${optIndex}`"
                    />
                    <span 
                        :id="`option-${question.id}-${optIndex}`"
                        class="text-xs sm:text-sm lg:text-base text-gray-900 break-words leading-relaxed flex-1 min-w-0"
                    >
                        {{ option }}
                    </span>
                </label>
            </div>
        </fieldset>

        <!-- Clear Response Button -->
        <footer
            v-if="question.selectedAnswer"
            class="mt-3 sm:mt-4 lg:mt-6 flex justify-end"
        >
            <button
                type="button"
                @click="$emit('clear')"
                class="inline-flex items-center gap-1.5 sm:gap-2 rounded-lg bg-red-50 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 cursor-pointer"
                :aria-label="`Clear selected answer for question ${questionNumber}`"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
                Clear Response
            </button>
        </footer>
    </article>
</template>

<script setup>
defineProps({
    question: Object,
    questionNumber: Number,
});

defineEmits(["select", "clear"]);
</script>
