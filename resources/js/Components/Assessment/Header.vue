<template>
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 sm:mb-6 border-b pb-3 sm:pb-4 gap-3 sm:gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
        Technical Assessment
      </h1>
      <p class="text-xs sm:text-sm text-gray-600 mt-1">
        Question {{ currentQuestion }} of {{ totalQuestions }}
      </p>
    </div>
    <div class="text-left md:text-right">
      <p class="text-xs sm:text-sm font-medium text-gray-600">
        Candidate: {{ candidateName }}
      </p>
      <p class="text-xs text-gray-400 break-all">
        {{ candidateEmail }}
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentQuestion: Number,
  totalQuestions: Number,
  candidateName: String,
  candidateEmail: String
})
</script>
