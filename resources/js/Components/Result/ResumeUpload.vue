<template>
  <div>
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 text-left">
      <p class="text-green-700 font-bold">
        Congratulations! You passed the assessment.
      </p>
      <p class="text-green-600 text-sm">
        Please upload your resume to complete the application.
      </p>
    </div>

    <form @submit.prevent="$emit('submit')" class="mt-6">
      <div class="mb-4">
        <input
          type="file"
          @change="$emit('file-change', $event)"
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
        :disabled="isProcessing"
        class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ isProcessing ? 'Uploading...' : 'Upload Resume' }}
      </button>
    </form>
  </div>
</template>

<script setup>
defineProps({
  isProcessing: Boolean
})

defineEmits(['submit', 'file-change'])
</script>
