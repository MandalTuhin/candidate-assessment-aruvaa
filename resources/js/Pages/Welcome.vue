<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl w-full max-w-md">
      <h1 class="text-2xl sm:text-3xl font-bold text-center mb-2 text-gray-800">
        Technical Assessment
      </h1>
      <p class="text-center text-gray-600 mb-6 text-sm sm:text-base">
        Please enter your details to begin
      </p>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Full Name
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="John Doe"
          />
        </div>

        <div class="mb-6">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="john@example.com"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!form.processing">Start Assessment</span>
          <span v-else>Starting...</span>
        </button>
      </form>

      <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
          <strong>Note:</strong> This assessment contains 10 questions and must be completed in 5 minutes.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: ''
})

const submit = () => {
  form.post('/start-test')
}
</script>
