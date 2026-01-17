<template>
    <section class="mb-4 sm:mb-6" aria-label="Assessment progress">
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs sm:text-sm font-medium text-gray-700"
                >Completion Progress</span
            >
            <span class="text-xs sm:text-sm font-medium text-gray-700" aria-live="polite">
                {{ answeredCount }} of {{ totalQuestions }} answered ({{
                    Math.round((answeredCount / totalQuestions) * 100)
                }}%)
            </span>
        </div>
        <div 
            class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5"
            role="progressbar"
            :aria-valuenow="answeredCount"
            :aria-valuemin="0"
            :aria-valuemax="totalQuestions"
            :aria-label="`Progress: ${answeredCount} of ${totalQuestions} questions answered`"
        >
            <div
                class="bg-green-600 h-2 sm:h-2.5 rounded-full transition-all duration-300"
                :style="`width: ${(answeredCount / totalQuestions) * 100}%`"
            ></div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    answeredCount: Number,
    totalQuestions: Number,
});
</script>
