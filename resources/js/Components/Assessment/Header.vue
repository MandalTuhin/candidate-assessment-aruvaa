<template>
    <header
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 sm:mb-4 lg:mb-6 border-b border-gray-300 pb-2 sm:pb-3 lg:pb-4 gap-2 sm:gap-3 lg:gap-4"
        role="banner"
        aria-label="Assessment header"
    >
        <section aria-label="Assessment progress" class="min-w-0 flex-1">
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 break-words">
                Technical Assessment
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1" aria-live="polite">
                Question {{ currentQuestion }} of {{ totalQuestions }}
            </p>
        </section>
        <section class="text-left sm:text-right shrink-0" aria-label="Candidate information">
            <p class="text-xs sm:text-sm font-medium text-gray-600 break-words">
                Candidate: {{ candidateName }}
            </p>
            <p class="text-xs text-gray-400 break-all">
                {{ candidateEmail }}
            </p>
        </section>
    </header>
</template>

<script setup>
defineProps({
    currentQuestion: Number,
    totalQuestions: Number,
    candidateName: String,
    candidateEmail: String,
});
</script>
