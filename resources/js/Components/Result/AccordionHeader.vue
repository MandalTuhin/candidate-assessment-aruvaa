<template>
    <button
        @click="$emit('toggle')"
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
                />
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
            :class="{ 'rotate-180': isOpen }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>
    </button>
</template>

<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
});

defineEmits(['toggle']);
</script>