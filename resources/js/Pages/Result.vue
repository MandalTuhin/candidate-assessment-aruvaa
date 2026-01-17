<template>
    <main
        class="bg-gray-100 min-h-screen flex items-center justify-center p-3 sm:p-4"
        role="main"
        aria-label="Assessment results"
    >
        <article
            class="bg-white p-4 sm:p-6 md:p-8 rounded-lg shadow-lg w-full max-w-2xl text-center"
        >
            <!-- Error Messages -->
            <section
                v-if="
                    $page.props.errors &&
                    Object.keys($page.props.errors).length > 0
                "
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
                role="alert"
                aria-live="polite"
                aria-label="Form validation errors"
            >
                <ul>
                    <li v-for="(error, key) in $page.props.errors" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </section>

            <!-- Success Message -->
            <section
                v-if="$page.props.flash?.success"
                class="bg-green-500 text-white p-3 rounded mb-4"
                role="alert"
                aria-live="polite"
                aria-label="Success notification"
            >
                {{ $page.props.flash.success }}
            </section>

            <header class="mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">
                    Assessment Result
                </h1>
                <p class="text-sm sm:text-base text-gray-600 wrap-break-word">
                    Prepared for: <strong>{{ candidateName }}</strong>
                </p>
            </header>

            <section 
                class="mb-4 sm:mb-6"
                aria-label="Final score"
            >
                <div
                    class="text-5xl sm:text-6xl font-extrabold"
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
