<template>
  <div class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-lg shadow-md w-full max-w-md">
      <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center text-gray-800">
        Select Programming Languages
      </h1>

      <form @submit.prevent="submit">
        <div class="space-y-4 mb-6">
          <!-- Error Messages -->
          <div v-if="form.errors && Object.keys(form.errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
              <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
            </ul>
          </div>

          <!-- Language Checkboxes -->
          <div v-for="language in languages" :key="language.id" class="flex items-center">
            <input
              type="checkbox"
              :id="`lang-${language.id}`"
              :value="language.id"
              v-model="form.languages"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label
              :for="`lang-${language.id}`"
              class="ml-3 text-gray-700 font-medium cursor-pointer"
            >
              {{ language.name }}
            </label>
          </div>

          <!-- Name and Email Fields -->
          <div class="mb-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Full Name
              </label>
              <input
                type="text"
                v-model="form.name"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Email Address
              </label>
              <input
                type="email"
                v-model="form.email"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
              />
            </div>
          </div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!form.processing">Start Test</span>
          <span v-else>Starting...</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  languages: Array
})

const form = useForm({
  name: '',
  email: '',
  languages: []
})

const submit = () => {
  form.post('/start-test')
}
</script>
