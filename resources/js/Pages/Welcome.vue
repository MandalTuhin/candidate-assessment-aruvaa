<template>
    <main class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
        <section class="w-full max-w-md sm:max-w-lg lg:max-w-xl bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8 lg:p-10">
            <header class="mb-6 sm:mb-8">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-center text-gray-900 leading-tight">
                    Select Programming Languages
                </h1>
            </header>

            <!-- Database Error Message -->
            <div
                v-if="error"
                class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"
                role="alert"
                aria-live="polite"
            >
                <p class="font-semibold">Connection Error</p>
                <p class="mt-1 text-sm">{{ error }}</p>
            </div>

            <!-- Flash Messages -->
            <div
                v-if="$page.props.flash?.error"
                class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"
                role="alert"
                aria-live="polite"
            >
                {{ $page.props.flash.error }}
            </div>

            <form @submit.prevent="submit" role="form" aria-label="Assessment setup form">
                <!-- Validation Error Messages -->
                <div
                    v-if="form.errors && Object.keys(form.errors).length > 0"
                    class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"
                    role="alert"
                    aria-live="polite"
                >
                    <ul class="space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key" class="text-sm">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <LanguageSelector
                    :languages="languages"
                    :selected-languages="form.languages"
                    @toggle="toggleLanguage"
                />

                <CandidateForm
                    v-model:name="form.name"
                    v-model:email="form.email"
                />

                <button
                    type="submit"
                    :disabled="form.processing || languages.length === 0 || !isFormValid"
                    :class="[
                        'w-full rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600',
                        isFormValid && !form.processing && languages.length > 0
                            ? 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer'
                            : 'bg-gray-300 text-gray-500 cursor-not-allowed opacity-60'
                    ]"
                    :aria-label="languages.length === 0 ? 'Loading languages...' : !isFormValid ? 'Please fill in all required information' : 'Start technical assessment'"
                >
                    <span v-if="!form.processing">
                        {{ languages.length === 0 ? 'Loading...' : 'Start Test' }}
                    </span>
                    <span v-else aria-live="polite">Starting...</span>
                </button>
            </form>
        </section>
    </main>
</template>

<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import LanguageSelector from "@/Components/Welcome/LanguageSelector.vue";
import CandidateForm from "@/Components/Welcome/CandidateForm.vue";

const props = defineProps({
    languages: Array,
    error: String,
});

const form = useForm({
    name: "",
    email: "",
    languages: [],
});

const isFormValid = computed(() => {
    return form.name.trim() !== "" && 
           form.email.trim() !== "" && 
           form.languages.length > 0;
});

const toggleLanguage = (languageId) => {
    const index = form.languages.indexOf(languageId);
    if (index > -1) {
        form.languages.splice(index, 1);
    } else {
        form.languages.push(languageId);
    }
};

const submit = () => {
    form.post("/start-test");
};
</script>
