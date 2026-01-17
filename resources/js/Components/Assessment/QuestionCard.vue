<template>
  <div class="mb-6 sm:mb-8 p-3 sm:p-4 border-l-4 border-blue-500 bg-gray-50">
    <div class="flex items-start justify-between mb-2">
      <p class="font-bold text-base sm:text-lg flex-1 wrap-break-word">
        <span>Q{{ questionNumber }}:</span>
        <span>{{ question.question_text }}</span>
      </p>
    </div>
    <span class="text-xs sm:text-sm text-blue-600 font-mono">[{{ question.language_name }}]</span>

    <div class="mt-3 sm:mt-4 space-y-2">
      <label
        v-for="(option, optIndex) in question.options"
        :key="optIndex"
        class="flex items-center space-x-2 sm:space-x-3 p-3 sm:p-2 border rounded hover:bg-gray-100 cursor-pointer transition-colors touch-manipulation"
        :class="{
          'bg-blue-100 border-blue-400': question.selectedAnswer === option
        }"
      >
        <input
          type="radio"
          :name="`current_answer_${question.id}`"
          :value="option"
          :checked="question.selectedAnswer === option"
          @change="$emit('select', option)"
          class="h-5 w-5 sm:h-4 sm:w-4 shrink-0"
        />
        <span class="text-sm sm:text-base wrap-break-word">{{ option }}</span>
      </label>
    </div>

    <!-- Clear Response Button -->
    <div v-if="question.selectedAnswer" class="mt-3 sm:mt-4 flex justify-end">
      <button
        type="button"
        @click="$emit('clear')"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs sm:text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-md hover:bg-red-100 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-400 transition-all touch-manipulation cursor-pointer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Clear Response
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  question: Object,
  questionNumber: Number
})

defineEmits(['select', 'clear'])
</script>
