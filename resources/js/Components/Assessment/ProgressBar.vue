<template>
    <div class="mb-4 sm:mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs sm:text-sm font-medium text-gray-700"
                >Completion Progress</span
            >
            <span class="text-xs sm:text-sm font-medium text-gray-700">
                {{ answeredCount }} of {{ totalQuestions }} answered ({{
                    Math.round((answeredCount / totalQuestions) * 100)
                }}%)
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5">
            <div
                class="bg-green-600 h-2 sm:h-2.5 rounded-full transition-all duration-300"
                :style="`width: ${(answeredCount / totalQuestions) * 100}%`"
            ></div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    answeredCount: Number,
    totalQuestions: Number,
});
</script>
