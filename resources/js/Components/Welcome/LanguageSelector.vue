<template>
    <fieldset class="mb-6 space-y-3 sm:space-y-4">
        <legend class="text-sm font-medium text-gray-900 mb-4">
            Select Programming Languages (choose at least one):
        </legend>
        <div class="space-y-3">
            <div
                v-for="language in languages"
                :key="language.id"
                class="relative flex items-start"
            >
                <div class="flex h-6 items-center">
                    <input
                        type="checkbox"
                        :id="`lang-${language.id}`"
                        :value="language.id"
                        :checked="selectedLanguages.includes(language.id)"
                        @change="toggleLanguage(language.id)"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                        :aria-describedby="`lang-${language.id}-desc`"
                    />
                </div>
                <div class="ml-3 text-sm leading-6">
                    <label
                        :for="`lang-${language.id}`"
                        class="font-medium text-gray-900 cursor-pointer"
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
            </div>
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
