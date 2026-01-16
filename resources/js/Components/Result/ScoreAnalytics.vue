<template>
  <div class="mb-6 sm:mb-8 bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-200 text-left">
    <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-gray-800">Score Analytics</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
      <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
        <div class="text-2xl sm:text-3xl font-bold text-blue-600">
          {{ analytics.total_questions || 0 }}
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mt-1">Total Questions</div>
      </div>
      <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
        <div class="text-2xl sm:text-3xl font-bold text-green-600">
          {{ analytics.correct || 0 }}
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mt-1">Correct</div>
      </div>
      <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
        <div class="text-2xl sm:text-3xl font-bold text-red-600">
          {{ analytics.incorrect || 0 }}
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mt-1">Incorrect</div>
      </div>
      <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
        <div class="text-2xl sm:text-3xl font-bold text-yellow-600">
          {{ analytics.skipped || 0 }}
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mt-1">Skipped</div>
      </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-200">
      <div class="text-center">
        <span class="text-sm text-gray-600">Questions Answered: </span>
        <span class="text-lg font-bold text-gray-800">
          {{ analytics.answered || 0 }} / {{ analytics.total_questions || 0 }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  analytics: {
    type: Object,
    required: true
  }
})
</script>
