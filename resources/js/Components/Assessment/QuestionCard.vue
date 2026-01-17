<template>
    <article class="mb-6 sm:mb-8 rounded-lg border-l-4 border-blue-500 bg-gray-50 p-4 sm:p-6">
        <header class="mb-4">
            <h2 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 break-words">
                <span class="text-blue-600">Q{{ questionNumber }}:</span>
                <span class="ml-2">{{ question.question_text }}</span>
            </h2>
            <span class="mt-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                {{ question.language_name }}
            </span>
        </header>

        <fieldset class="space-y-3 sm:space-y-4" :aria-labelledby="`question-${question.id}-legend`">
            <legend :id="`question-${question.id}-legend`" class="sr-only">
                Question {{ questionNumber }}: {{ question.question_text }}
            </legend>
            
            <div
                v-for="(option, optIndex) in question.options"
                :key="optIndex"
                class="relative"
            >
                <label
                    class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 sm:p-4 transition-all hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-200 has-[:checked]:ring-1 has-[:checked]:ring-blue-600"
                >
                    <input
                        type="radio"
                        :name="`current_answer_${question.id}`"
                        :value="option"
                        :checked="question.selectedAnswer === option"
                        @change="$emit('select', option)"
                        class="mt-1 h-4 w-4 shrink-0 border-gray-300 text-blue-600 focus:ring-blue-600"
                        :aria-describedby="`option-${question.id}-${optIndex}`"
                    />
                    <span 
                        :id="`option-${question.id}-${optIndex}`"
                        class="text-sm sm:text-base text-gray-900 break-words leading-relaxed"
                    >
                        {{ option }}
                    </span>
                </label>
            </div>
        </fieldset>

        <!-- Clear Response Button -->
        <footer
            v-if="question.selectedAnswer"
            class="mt-4 sm:mt-6 flex justify-end"
        >
            <button
                type="button"
                @click="$emit('clear')"
                class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
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
