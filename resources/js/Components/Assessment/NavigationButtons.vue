<template>
    <footer
        class="mt-6 sm:mt-8 flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-between sm:gap-4"
        role="navigation"
        aria-label="Assessment navigation controls"
    >
        <button
            type="button"
            @click="$emit('previous')"
            :disabled="!canGoPrevious || isLoading"
            class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:order-1"
            :class="{
                'bg-gray-100 text-gray-400 cursor-not-allowed':
                    !canGoPrevious || isLoading,
                'bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-gray-600 cursor-pointer':
                    canGoPrevious && !isLoading,
            }"
            :aria-label="isLoading ? 'Loading previous question' : 'Go to previous question'"
        >
            <svg
                v-if="isLoading"
                class="h-4 w-4 animate-spin"
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
            {{ isLoading ? "Loading..." : "Previous" }}
        </button>

        <button
            type="button"
            @click="$emit('submit')"
            :disabled="isSubmitting"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer sm:order-2"
            :aria-label="isSubmitting ? 'Submitting assessment...' : 'Submit assessment for grading'"
        >
            <svg
                v-if="isSubmitting"
                class="h-4 w-4 animate-spin"
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
            {{ isSubmitting ? "Submitting..." : "Submit Test" }}
        </button>

        <button
            type="button"
            @click="$emit('next')"
            :disabled="!canGoNext || isLoading"
            class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:order-3"
            :class="{
                'bg-gray-100 text-gray-400 cursor-not-allowed':
                    !canGoNext || isLoading,
                'bg-blue-600 text-white hover:bg-blue-500 focus-visible:outline-blue-600 cursor-pointer':
                    canGoNext && !isLoading,
            }"
            :aria-label="isLoading ? 'Loading next question' : 'Go to next question'"
        >
            {{ isLoading ? "Loading..." : "Next" }}
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
                class="h-4 w-4 animate-spin"
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
