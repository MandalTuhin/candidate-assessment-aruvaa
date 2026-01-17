<template>
    <div
        class="mb-4 sm:mb-6 bg-gray-50 border border-gray-200 rounded-lg overflow-hidden"
    >
        <button
            type="button"
            @click.stop="miniMapOpen = !miniMapOpen"
            class="flex justify-between items-center w-full p-3 sm:p-4 sm:pb-3 sm:cursor-default sm:pointer-events-none cursor-pointer sm:mb-3"
        >
            <span class="text-xs sm:text-sm font-medium text-gray-700"
                >Question Navigator</span
            >
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 hidden sm:inline"
                    >Click to jump to question</span
                >
                <svg
                    class="h-5 w-5 text-gray-600 transition-transform duration-200 sm:hidden"
                    :class="{ 'rotate-180': miniMapOpen }"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
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
        <div class="hidden sm:block sm:px-4 sm:pb-4">
            <div class="grid grid-cols-5 sm:grid-cols-10 gap-2">
                <button
                    v-for="(question, index) in questions"
                    :key="question.id"
                    type="button"
                    @click="$emit('navigate', index)"
                    class="aspect-square cursor-pointer rounded-lg font-bold text-xs sm:text-sm transition-all duration-200 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-blue-400 touch-manipulation"
                    :class="{
                        'bg-blue-600 text-white shadow-md':
                            currentIndex === index,
                        'bg-green-500 text-white':
                            currentIndex !== index && question.selectedAnswer,
                        'bg-gray-200 text-gray-600 hover:bg-gray-300':
                            currentIndex !== index && !question.selectedAnswer,
                    }"
                    :title="`Question ${index + 1}${
                        question.selectedAnswer
                            ? ' (Answered)'
                            : ' (Not answered)'
                    }`"
                >
                    {{ index + 1 }}
                </button>
            </div>
            <div class="flex flex-wrap gap-3 sm:gap-4 mt-3 text-xs">
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 bg-blue-600 rounded"></div>
                    <span class="text-gray-600">Current</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-gray-600">Answered</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 bg-gray-200 rounded"></div>
                    <span class="text-gray-600">Not Answered</span>
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
            <div v-show="miniMapOpen" class="sm:hidden origin-top">
                <div class="px-3 pb-3 border-t border-gray-200">
                    <div class="grid grid-cols-5 gap-2 mt-3 mb-3">
                        <button
                            v-for="(question, index) in questions"
                            :key="question.id"
                            type="button"
                            @click="$emit('navigate', index)"
                            class="aspect-square rounded-lg font-bold text-xs transition-all duration-200 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-blue-400 touch-manipulation cursor-pointer"
                            :class="{
                                'bg-blue-600 text-white shadow-md':
                                    currentIndex === index,
                                'bg-green-500 text-white':
                                    currentIndex !== index &&
                                    question.selectedAnswer,
                                'bg-gray-200 text-gray-600 hover:bg-gray-300':
                                    currentIndex !== index &&
                                    !question.selectedAnswer,
                            }"
                            :title="`Question ${index + 1}${
                                question.selectedAnswer
                                    ? ' (Answered)'
                                    : ' (Not answered)'
                            }`"
                        >
                            {{ index + 1 }}
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-blue-600 rounded"></div>
                            <span class="text-gray-600">Current</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-green-500 rounded"></div>
                            <span class="text-gray-600">Answered</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-gray-200 rounded"></div>
                            <span class="text-gray-600">Not Answered</span>
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
    questions: Array,
    currentIndex: Number,
});

defineEmits(["navigate"]);

const miniMapOpen = ref(false);
</script>
