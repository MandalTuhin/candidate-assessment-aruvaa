<template>
    <main
        class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8"
        role="main"
        aria-label="Assessment results"
    >
        <article
            class="w-full max-w-2xl lg:max-w-4xl rounded-xl bg-white shadow-lg border border-gray-200 p-6 sm:p-8 lg:p-10 text-center"
        >
            <!-- Error Messages -->
            <section
                v-if="
                    $page.props.errors &&
                    Object.keys($page.props.errors).length > 0
                "
                class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"
                role="alert"
                aria-live="polite"
                aria-label="Form validation errors"
            >
                <ul class="space-y-1">
                    <li v-for="(error, key) in $page.props.errors" :key="key" class="text-sm">
                        {{ error }}
                    </li>
                </ul>
            </section>

            <!-- Success Message -->
            <section
                v-if="$page.props.flash?.success"
                class="mb-6 rounded-lg bg-green-600 p-4 text-white"
                role="alert"
                aria-live="polite"
                aria-label="Success notification"
            >
                {{ $page.props.flash.success }}
            </section>

            <header class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3">
                    Assessment Result
                </h1>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600 break-words">
                    Prepared for: <strong>{{ candidateName }}</strong>
                </p>
            </header>

            <section 
                class="mb-6 sm:mb-8"
                aria-label="Final score"
            >
                <div
                    class="text-5xl sm:text-6xl lg:text-7xl font-extrabold"
                    :class="passed ? 'text-green-600' : 'text-red-600'"
                    :aria-label="`Final score: ${score} percent. ${passed ? 'Passed' : 'Failed'}`"
                >
                    {{ score }}%
                </div>
            </section>

            <ScoreAnalytics
                v-if="analytics && Object.keys(analytics).length > 0"
                :analytics="analytics"
            />

            <ReviewAccordion
                v-if="allQuestionsReview && allQuestionsReview.length > 0"
                :questions="allQuestionsReview"
            />

            <section v-if="passed" aria-label="Resume upload section">
                <ResumeUpload
                    :is-processing="uploadForm.processing"
                    @submit="uploadResume"
                    @file-change="handleFileChange"
                />
            </section>

            <section v-else aria-label="Assessment failed message">
                <FailedMessage />
            </section>
        </article>
    </main>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import ScoreAnalytics from "@/Components/Result/ScoreAnalytics.vue";
import ReviewAccordion from "@/Components/Result/ReviewAccordion.vue";
import ResumeUpload from "@/Components/Result/ResumeUpload.vue";
import FailedMessage from "@/Components/Result/FailedMessage.vue";

const props = defineProps({
    score: Number,
    passed: Boolean,
    assessmentId: Number,
    analytics: Object,
    allQuestionsReview: Array,
    candidateName: String,
});

const uploadForm = useForm({
    resume: null,
    assessment_id: props.assessmentId,
});

const handleFileChange = (event) => {
    uploadForm.resume = event.target.files[0];
};

const uploadResume = () => {
    uploadForm.post("/upload-resume");
};
</script>
