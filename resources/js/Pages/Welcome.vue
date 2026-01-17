<template>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1
                class="lg:text-2xl text-xl font-bold mb-8 lg:mb-7 text-center text-gray-800"
            >
                Select Programming Languages
            </h1>

            <form @submit.prevent="submit">
                <!-- Error Messages -->
                <div
                    v-if="form.errors && Object.keys(form.errors).length > 0"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
                >
                    <ul>
                        <li v-for="(error, key) in form.errors" :key="key">
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
                    :disabled="form.processing"
                    class="w-full bg-blue-600 cursor-pointer text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="!form.processing">Start Test</span>
                    <span v-else>Starting...</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import LanguageSelector from "@/Components/Welcome/LanguageSelector.vue";
import CandidateForm from "@/Components/Welcome/CandidateForm.vue";

const props = defineProps({
    languages: Array,
});

const form = useForm({
    name: "",
    email: "",
    languages: [],
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
