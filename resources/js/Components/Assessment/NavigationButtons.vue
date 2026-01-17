<template>
    <footer
        class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-0 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t"
        role="navigation"
        aria-label="Assessment navigation controls"
    >
        <button
            type="button"
            @click="$emit('previous')"
            :disabled="!canGoPrevious || isLoading"
            class="px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
            :class="{
                'bg-gray-300 text-gray-500 cursor-not-allowed':
                    !canGoPrevious || isLoading,
                'bg-gray-600 text-white hover:bg-gray-700 cursor-pointer':
                    canGoPrevious && !isLoading,
            }"
            :aria-label="isLoading ? 'Loading previous question' : 'Go to previous question'"
        >
            <svg
                v-if="isLoading"
                class="animate-spin h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <svg
                v-else
                class="h-4 w-4"
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
                    d="M15 19l-7-7 7-7"
                />
            </svg>
            <span class="text-sm sm:text-base">{{
                isLoading ? "Loading..." : "Previous"
            }}</span>
        </button>

        <button
            type="button"
            @click="$emit('submit')"
            :disabled="isSubmitting"
            class="bg-red-600 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer touch-manipulation order-first sm:order-0"
            :aria-label="isSubmitting ? 'Submitting assessment...' : 'Submit assessment for grading'"
        >
            <svg
                v-if="isSubmitting"
                class="animate-spin h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <span class="text-sm sm:text-base">{{
                isSubmitting ? "Submitting..." : "Submit Test"
            }}</span>
        </button>

        <button
            type="button"
            @click="$emit('next')"
            :disabled="!canGoNext || isLoading"
            class="px-4 sm:px-6 py-2.5 sm:py-2 cursor-pointer rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
            :class="{
                'bg-gray-300 text-gray-500 cursor-not-allowed':
                    !canGoNext || isLoading,
                'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer':
                    canGoNext && !isLoading,
            }"
            :aria-label="isLoading ? 'Loading next question' : 'Go to next question'"
        >
            <span class="text-sm sm:text-base">{{
                isLoading ? "Loading..." : "Next"
            }}</span>
            <svg
                v-if="!isLoading"
                class="h-4 w-4"
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
                    d="M9 5l7 7-7 7"
                />
            </svg>
            <svg
                v-else
                class="animate-spin h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
        </button>
    </footer>
</template>

<script setup>
defineProps({
    canGoPrevious: Boolean,
    canGoNext: Boolean,
    isLoading: Boolean,
    isSubmitting: Boolean,
});

defineEmits(["previous", "next", "submit"]);
</script>
