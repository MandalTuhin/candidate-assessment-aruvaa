<template>
    <div
        class="bg-gray-100 min-h-screen flex items-center justify-center p-3 sm:p-4"
    >
        <div
            class="bg-white p-4 sm:p-6 md:p-8 rounded-lg shadow-lg w-full max-w-2xl text-center"
        >
            <!-- Error Messages -->
            <div
                v-if="
                    $page.props.errors &&
                    Object.keys($page.props.errors).length > 0
                "
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
            >
                <ul class="list-disc pl-5">
                    <li v-for="(error, key) in $page.props.errors" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Success Message -->
            <div
                v-if="$page.props.flash?.success"
                class="bg-green-500 text-white p-3 rounded mb-4"
            >
                {{ $page.props.flash.success }}
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold mb-2">
                Assessment Result
            </h1>
            <p class="text-sm sm:text-base text-gray-600 mb-4 wrap-break-word">
                Prepared for: <strong>{{ candidateName }}</strong>
            </p>

            <div
                class="text-5xl sm:text-6xl font-extrabold mb-4 sm:mb-6"
                :class="passed ? 'text-green-600' : 'text-red-600'"
            >
                {{ score }}%
            </div>

            <ScoreAnalytics
                v-if="analytics && Object.keys(analytics).length > 0"
                :analytics="analytics"
            />

            <ReviewAccordion
                v-if="allQuestionsReview && allQuestionsReview.length > 0"
                :questions="allQuestionsReview"
            />

            <ResumeUpload
                v-if="passed"
                :is-processing="uploadForm.processing"
                @submit="uploadResume"
                @file-change="handleFileChange"
            />

            <FailedMessage v-else />
        </div>
    </div>
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
