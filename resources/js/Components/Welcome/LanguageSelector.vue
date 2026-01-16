<template>
  <div class="space-y-4 mb-6">
    <div v-for="language in languages" :key="language.id" class="flex items-center">
      <input
        type="checkbox"
        :id="`lang-${language.id}`"
        :value="language.id"
        :checked="selectedLanguages.includes(language.id)"
        @change="toggleLanguage(language.id)"
        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
      />
      <label
        :for="`lang-${language.id}`"
        class="ml-3 text-gray-700 font-medium cursor-pointer"
      >
        {{ language.name }}
      </label>
    </div>
  </div>
</template>

<script setup>
defineProps({
  languages: Array,
  selectedLanguages: Array
})

const emit = defineEmits(['update:selectedLanguages'])

const toggleLanguage = (languageId) => {
  emit('toggle', languageId)
}
</script>
