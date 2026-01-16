<template>
  <div class="bg-gray-100 min-h-screen flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-lg shadow-lg w-full max-w-2xl text-center">
      <!-- Error Messages -->
      <div v-if="$page.props.errors && Object.keys($page.props.errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5">
          <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="bg-green-500 text-white p-3 rounded mb-4">
        {{ $page.props.flash.success }}
      </div>

      <h1 class="text-2xl sm:text-3xl font-bold mb-2">Assessment Result</h1>
      <p class="text-sm sm:text-base text-gray-600 mb-4 break-words">
        Prepared for: <strong>{{ candidateName }}</strong>
      </p>

      <div 
        class="text-5xl sm:text-6xl font-extrabold mb-4 sm:mb-6"
        :class="passed ? 'text-green-600' : 'text-red-600'"
      >
        {{ score }}%
      </div>

      <!-- Score Analytics -->
      <ScoreAnalytics v-if="analytics && Object.keys(analytics).length > 0" :analytics="analytics" />

      <!-- Review All Questions -->
      <ReviewAccordion 
        v-if="allQuestionsReview && allQuestionsReview.length > 0" 
        :questions="allQuestionsReview" 
      />

      <!-- Passed Section -->
      <template v-if="passed">
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 text-left">
          <p class="text-green-700 font-bold">
            Congratulations! You passed the assessment.
          </p>
          <p class="text-green-600 text-sm">
            Please upload your resume to complete the application.
          </p>
        </div>

        <form @submit.prevent="uploadResume" class="mt-6">
          <div class="mb-4">
            <input
              type="file"
              @change="handleFileChange"
              accept=".pdf,.doc,.docx"
              required
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
            />
            <p class="text-xs text-gray-400 mt-2">
              Accepted formats: PDF, DOC, DOCX (Max 2MB)
            </p>
          </div>

          <button
            type="submit"
            :disabled="uploadForm.processing"
            class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ uploadForm.processing ? 'Uploading...' : 'Upload Resume' }}
          </button>
        </form>
      </template>

      <!-- Failed Section -->
      <template v-else>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 text-left">
          <p class="text-red-700 font-bold">
            Thank you for your interest.
          </p>
          <p class="text-red-600 text-sm">
            Unfortunately, your score did not meet the required threshold. Please try again later.
          </p>
        </div>

        <Link href="/" class="mt-6 inline-block text-blue-600 hover:underline">
          Return to Home
        </Link>
      </template>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import ScoreAnalytics from '@/Components/Result/ScoreAnalytics.vue'
import ReviewAccordion from '@/Components/Result/ReviewAccordion.vue'

const props = defineProps({
  score: Number,
  passed: Boolean,
  assessmentId: Number,
  analytics: Object,
  allQuestionsReview: Array,
  candidateName: String
})

const uploadForm = useForm({
  resume: null,
  assessment_id: props.assessmentId
})

const handleFileChange = (event) => {
  uploadForm.resume = event.target.files[0]
}

const uploadResume = () => {
  uploadForm.post('/upload-resume')
}
</script>
