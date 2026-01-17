<template>
    <header
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 sm:mb-6 border-b border-gray-300 pb-3 sm:pb-4 gap-3 sm:gap-4"
        role="banner"
        aria-label="Assessment header"
    >
        <section aria-label="Assessment progress">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                Technical Assessment
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1" aria-live="polite">
                Question {{ currentQuestion }} of {{ totalQuestions }}
            </p>
        </section>
        <section class="text-left md:text-right" aria-label="Candidate information">
            <p class="text-xs sm:text-sm font-medium text-gray-600">
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
