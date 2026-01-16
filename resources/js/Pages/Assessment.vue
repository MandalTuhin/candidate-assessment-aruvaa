<template>
  <div class="bg-gray-100 p-4 md:p-8 min-h-screen">
    <!-- Loading Overlay -->
    <div 
      v-show="isPageLoading" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg flex items-center gap-3 mx-4">
        <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-base sm:text-lg font-medium text-gray-700">Loading assessment...</span>
      </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white p-4 sm:p-6 rounded shadow" :class="{ 'opacity-50 pointer-events-none': isPageLoading }">
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 sm:mb-6 border-b pb-3 sm:pb-4 gap-3 sm:gap-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            Technical Assessment
          </h1>
          <p class="text-xs sm:text-sm text-gray-600 mt-1">
            Question {{ currentQuestionIndex + 1 }} of {{ totalQuestions }}
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

      <!-- Progress Bar -->
      <div class="mb-4 sm:mb-6">
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs sm:text-sm font-medium text-gray-700">Completion Progress</span>
          <span class="text-xs sm:text-sm font-medium text-gray-700">
            {{ answeredCount }} of {{ totalQuestions }} answered
            ({{ Math.round((answeredCount / totalQuestions) * 100) }}%)
          </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5">
          <div 
            class="bg-green-600 h-2 sm:h-2.5 rounded-full transition-all duration-300"
            :style="`width: ${(answeredCount / totalQuestions) * 100}%`"
          ></div>
        </div>
      </div>

      <!-- Question MiniMap -->
      <MiniMap 
        :questions="questions"
        :current-index="currentQuestionIndex"
        @navigate="navigateToQuestion"
      />

      <!-- Timer -->
      <div class="mb-4 sm:mb-6 p-2 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex justify-between items-center">
          <span class="text-xs sm:text-sm font-medium text-gray-700">Time Remaining:</span>
          <span 
            class="text-base sm:text-lg font-bold"
            :class="{
              'text-red-600': timeRemaining <= 60,
              'text-yellow-600': timeRemaining <= 120 && timeRemaining > 60,
              'text-green-600': timeRemaining > 120
            }"
          >
            {{ formatTime(timeRemaining) }}
          </span>
        </div>
      </div>

      <!-- Question Form -->
      <form @submit.prevent="submitTest">
        <!-- Current Question Display -->
        <div 
          v-for="(question, index) in questions" 
          :key="question.id"
          v-show="currentQuestionIndex === index"
          class="mb-6 sm:mb-8 p-3 sm:p-4 border-l-4 border-blue-500 bg-gray-50"
        >
          <div class="flex items-start justify-between mb-2">
            <p class="font-bold text-base sm:text-lg flex-1 wrap-break-word">
              <span>Q{{ index + 1 }}:</span>
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
                @change="selectAnswer(question, option)"
                class="h-5 w-5 sm:h-4 sm:w-4 shrink-0"
              />
              <span class="text-sm sm:text-base wrap-break-word">{{ option }}</span>
            </label>
          </div>

          <!-- Clear Response Button -->
          <div v-if="question.selectedAnswer" class="mt-3 sm:mt-4 flex justify-end">
            <button
              type="button"
              @click="clearAnswer(question)"
              class="inline-flex items-center gap-2 px-3 py-1.5 text-xs sm:text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-md hover:bg-red-100 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-400 transition-all touch-manipulation"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Clear Response
            </button>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-0 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t">
          <button
            type="button"
            @click="previousQuestion"
            :disabled="currentQuestionIndex === 0 || isLoading"
            class="px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
            :class="{
              'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === 0 || isLoading,
              'bg-gray-600 text-white hover:bg-gray-700': currentQuestionIndex > 0 && !isLoading
            }"
          >
            <svg v-if="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm sm:text-base">{{ isLoading ? 'Loading...' : 'Previous' }}</span>
          </button>

          <button
            type="button"
            @click="submitTest"
            :disabled="isSubmitting"
            class="bg-red-600 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation order-first sm:order-0"
          >
            <svg v-if="isSubmitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm sm:text-base">{{ isSubmitting ? 'Submitting...' : 'Submit Test' }}</span>
          </button>

          <button
            type="button"
            @click="nextQuestion"
            :disabled="currentQuestionIndex === questions.length - 1 || isLoading"
            class="px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
            :class="{
              'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === questions.length - 1 || isLoading,
              'bg-blue-600 text-white hover:bg-blue-700': currentQuestionIndex < questions.length - 1 && !isLoading
            }"
          >
            <span class="text-sm sm:text-base">{{ isLoading ? 'Loading...' : 'Next' }}</span>
            <svg v-if="!isLoading" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <svg v-else class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import MiniMap from '@/Components/Assessment/MiniMap.vue'

const props = defineProps({
  questionsData: Array,
  candidateName: String,
  candidateEmail: String
})

const questions = ref(props.questionsData)
const currentQuestionIndex = ref(0)
const timeRemaining = ref(300) // 5 minutes
const timerInterval = ref(null)
const isLoading = ref(false)
const isSubmitting = ref(false)
const isPageLoading = ref(true)

const totalQuestions = computed(() => questions.value.length)
const answeredCount = computed(() => questions.value.filter(q => q.selectedAnswer).length)

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const selectAnswer = (question, answer) => {
  question.selectedAnswer = answer
  saveProgress()
}

const clearAnswer = (question) => {
  question.selectedAnswer = null
  saveProgress()
}

const saveProgress = async () => {
  try {
    const answers = {}
    questions.value.forEach(q => {
      if (q.selectedAnswer) {
        answers[q.id] = q.selectedAnswer
      }
    })

    await fetch('/save-progress', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ answers })
    })
  } catch (error) {
    console.error('Failed to save progress:', error)
  }
}

const nextQuestion = async () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    isLoading.value = true
    await saveProgress()
    currentQuestionIndex.value++
    isLoading.value = false
  }
}

const previousQuestion = async () => {
  if (currentQuestionIndex.value > 0) {
    isLoading.value = true
    await saveProgress()
    currentQuestionIndex.value--
    isLoading.value = false
  }
}

const navigateToQuestion = async (index) => {
  if (index >= 0 && index < questions.value.length && index !== currentQuestionIndex.value) {
    isLoading.value = true
    await saveProgress()
    currentQuestionIndex.value = index
    isLoading.value = false
  }
}

const submitTest = async () => {
  if (isSubmitting.value) return
  
  isSubmitting.value = true
  
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
  
  await saveProgress()
  
  const answers = {}
  questions.value.forEach(question => {
    answers[question.id] = question.selectedAnswer || ''
  })
  
  router.post('/submit-test', { answers })
}

const startTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
  timerInterval.value = setInterval(() => {
    timeRemaining.value--
    if (timeRemaining.value <= 0) {
      handleTimeUp()
    }
  }, 1000)
}

const handleTimeUp = () => {
  clearInterval(timerInterval.value)
  submitTest()
}

onMounted(() => {
  // Add CSRF token meta tag if not present
  if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta')
    meta.name = 'csrf-token'
    meta.content = document.querySelector('input[name="_token"]')?.value || ''
    document.head.appendChild(meta)
  }

  setTimeout(() => {
    startTimer()
    saveProgress().then(() => {
      isPageLoading.value = false
    })
  }, 300)
})

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
})
</script>
