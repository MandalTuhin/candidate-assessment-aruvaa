<template>
    <section class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
        <button
            type="button"
            @click.stop="miniMapOpen = !miniMapOpen"
            class="flex w-full cursor-pointer items-center justify-between p-4 sm:cursor-default sm:pointer-events-none sm:pb-3"
            :aria-expanded="miniMapOpen"
            aria-controls="minimap-content"
            :aria-label="miniMapOpen ? 'Collapse question navigator' : 'Expand question navigator'"
        >
            <span class="text-sm font-medium text-gray-900">Question Navigator</span>
            <div class="flex items-center gap-2">
                <span class="hidden text-xs text-gray-500 sm:inline">Click to jump to question</span>
                <svg
                    class="h-5 w-5 text-gray-600 transition-transform duration-200 sm:hidden"
                    :class="{ 'rotate-180': miniMapOpen }"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </div>
        </button>

        <!-- Desktop: Always visible -->
        <div class="hidden px-4 pb-4 sm:block" id="minimap-content-desktop">
            <nav aria-label="Question navigation grid">
                <div class="grid grid-cols-8 gap-2 sm:grid-cols-10 lg:grid-cols-12" role="grid">
                    <button
                        v-for="(question, index) in questions"
                        :key="question.id"
                        type="button"
                        @click="$emit('navigate', index)"
                        class="aspect-square rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-105 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:text-sm cursor-pointer"
                        :class="{
                            'bg-blue-600 text-white shadow-md focus-visible:outline-blue-600':
                                currentIndex === index,
                            'bg-green-500 text-white hover:bg-green-600 focus-visible:outline-green-600':
                                currentIndex !== index && question.selectedAnswer,
                            'bg-gray-200 text-gray-700 hover:bg-gray-300 focus-visible:outline-gray-600':
                                currentIndex !== index && !question.selectedAnswer,
                        }"
                        :title="`Question ${index + 1}${
                            question.selectedAnswer
                                ? ' (Answered)'
                                : ' (Not answered)'
                        }`"
                        :aria-label="`Go to question ${index + 1}. ${
                            currentIndex === index ? 'Currently selected. ' : ''
                        }${question.selectedAnswer ? 'Answered' : 'Not answered'}`"
                        role="gridcell"
                    >
                        {{ index + 1 }}
                    </button>
                </div>
            </nav>
            <div class="mt-4 flex flex-wrap gap-4 text-xs" role="legend" aria-label="Question status legend">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded bg-blue-600" aria-hidden="true"></div>
                    <span class="text-gray-700">Current</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded bg-green-500" aria-hidden="true"></div>
                    <span class="text-gray-700">Answered</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded bg-gray-200" aria-hidden="true"></div>
                    <span class="text-gray-700">Not Answered</span>
                </div>
            </div>
        </div>

        <!-- Mobile: Collapsible version -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform scale-y-0"
            enter-to-class="opacity-100 transform scale-y-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform scale-y-100"
            leave-to-class="opacity-0 transform scale-y-0"
        >
            <div v-show="miniMapOpen" class="origin-top sm:hidden" id="minimap-content">
                <div class="border-t border-gray-200 px-4 pb-4">
                    <nav aria-label="Mobile question navigation grid" class="mt-4">
                        <div class="mb-4 grid grid-cols-6 gap-2" role="grid">
                            <button
                                v-for="(question, index) in questions"
                                :key="question.id"
                                type="button"
                                @click="$emit('navigate', index)"
                                class="aspect-square rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-105 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-pointer"
                                :class="{
                                    'bg-blue-600 text-white shadow-md focus-visible:outline-blue-600':
                                        currentIndex === index,
                                    'bg-green-500 text-white hover:bg-green-600 focus-visible:outline-green-600':
                                        currentIndex !== index &&
                                        question.selectedAnswer,
                                    'bg-gray-200 text-gray-700 hover:bg-gray-300 focus-visible:outline-gray-600':
                                        currentIndex !== index &&
                                        !question.selectedAnswer,
                                }"
                                :title="`Question ${index + 1}${
                                    question.selectedAnswer
                                        ? ' (Answered)'
                                        : ' (Not answered)'
                                }`"
                                :aria-label="`Go to question ${index + 1}. ${
                                    currentIndex === index ? 'Currently selected. ' : ''
                                }${question.selectedAnswer ? 'Answered' : 'Not answered'}`"
                                role="gridcell"
                            >
                                {{ index + 1 }}
                            </button>
                        </div>
                    </nav>
                    <div class="flex flex-wrap gap-3 text-xs" role="legend" aria-label="Question status legend">
                        <div class="flex items-center gap-1.5">
                            <div class="h-3 w-3 rounded bg-blue-600" aria-hidden="true"></div>
                            <span class="text-gray-700">Current</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="h-3 w-3 rounded bg-green-500" aria-hidden="true"></div>
                            <span class="text-gray-700">Answered</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="h-3 w-3 rounded bg-gray-200" aria-hidden="true"></div>
                            <span class="text-gray-700">Not Answered</span>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </section>
</template>

<script setup>
import { ref } from "vue";

defineProps({
    questions: Array,
    currentIndex: Number,
});

defineEmits(["navigate"]);

const miniMapOpen = ref(false);
</script>
