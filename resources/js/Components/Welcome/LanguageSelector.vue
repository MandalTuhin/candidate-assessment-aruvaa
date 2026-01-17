<template>
    <fieldset class="space-y-4 mb-6">
        <legend class="text-sm font-medium text-gray-700 mb-3">
            Select Programming Languages (choose at least one):
        </legend>
        <div
            v-for="language in languages"
            :key="language.id"
            class="flex items-center"
        >
            <input
                type="checkbox"
                :id="`lang-${language.id}`"
                :value="language.id"
                :checked="selectedLanguages.includes(language.id)"
                @change="toggleLanguage(language.id)"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                :aria-describedby="`lang-${language.id}-desc`"
            />
            <label
                :for="`lang-${language.id}`"
                class="ml-3 text-gray-700 font-medium cursor-pointer"
            >
                {{ language.name }}
            </label>
            <span 
                :id="`lang-${language.id}-desc`" 
                class="sr-only"
            >
                {{ language.description || `${language.name} programming language` }}
            </span>
        </div>
    </fieldset>
</template>

<script setup>
defineProps({
    languages: Array,
    selectedLanguages: Array,
});

const emit = defineEmits(["update:selectedLanguages"]);

const toggleLanguage = (languageId) => {
    emit("toggle", languageId);
};
</script>
