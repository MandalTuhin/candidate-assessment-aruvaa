<template>
    <main class="bg-gray-100 p-4 md:p-8 min-h-screen">
        <LoadingOverlay
            :is-loading="isPageLoading"
            message="Loading assessment..."
        />

        <section
            class="max-w-3xl mx-auto bg-white p-4 sm:p-6 rounded shadow"
            :class="{ 'opacity-50 pointer-events-none': isPageLoading }"
            role="main"
            aria-label="Technical assessment interface"
        >
            <Header
                :current-question="currentQuestionIndex + 1"
                :total-questions="totalQuestions"
                :candidate-name="candidateName"
                :candidate-email="candidateEmail"
            />

            <ProgressBar
                :answered-count="answeredCount"
                :total-questions="totalQuestions"
            />

            <nav aria-label="Question navigation">
                <MiniMap
                    :questions="questions"
                    :current-index="currentQuestionIndex"
                    @navigate="navigateToQuestion"
                />
            </nav>

            <Timer :time-remaining="timeRemaining" />

            <form @submit.prevent="submitTest" role="form" aria-label="Assessment questions form">
                <article
                    v-for="(question, index) in questions"
                    :key="question.id"
                    v-show="currentQuestionIndex === index"
                    role="article"
                    :aria-label="`Question ${index + 1} of ${totalQuestions}`"
                >
                    <QuestionCard
                        :question="question"
                        :question-number="index + 1"
                        @select="(option) => selectAnswer(question, option)"
                        @clear="clearAnswer(question)"
                    />
                </article>

                <nav aria-label="Assessment navigation controls">
                    <NavigationButtons
                        :can-go-previous="currentQuestionIndex > 0"
                        :can-go-next="currentQuestionIndex < questions.length - 1"
                        :is-loading="isLoading"
                        :is-submitting="isSubmitting"
                        @previous="previousQuestion"
                        @next="nextQuestion"
                        @submit="submitTest"
                    />
                </nav>
            </form>
        </section>
    </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import LoadingOverlay from "@/Components/Assessment/LoadingOverlay.vue";
import Header from "@/Components/Assessment/Header.vue";
import ProgressBar from "@/Components/Assessment/ProgressBar.vue";
import MiniMap from "@/Components/Assessment/MiniMap.vue";
import Timer from "@/Components/Assessment/Timer.vue";
import QuestionCard from "@/Components/Assessment/QuestionCard.vue";
import NavigationButtons from "@/Components/Assessment/NavigationButtons.vue";

const props = defineProps({
    questionsData: Array,
    candidateName: String,
    candidateEmail: String,
    timeRemaining: Number,
});

const questions = ref(props.questionsData);
const currentQuestionIndex = ref(0);
const timeRemaining = ref(props.timeRemaining);
const timerInterval = ref(null);
const isLoading = ref(false);
const isSubmitting = ref(false);
const isPageLoading = ref(true);

const totalQuestions = computed(() => questions.value.length);
const answeredCount = computed(
    () => questions.value.filter((q) => q.selectedAnswer).length,
);

const selectAnswer = (question, answer) => {
    question.selectedAnswer = answer;
    saveProgress();
};

const clearAnswer = (question) => {
    question.selectedAnswer = null;
    saveProgress();
};

const saveProgress = async () => {
    try {
        const answers = {};
        questions.value.forEach((q) => {
            if (q.selectedAnswer) {
                answers[q.id] = q.selectedAnswer;
            }
        });

        await fetch("/save-progress", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            body: JSON.stringify({ answers }),
        });
    } catch (error) {
        console.error("Failed to save progress:", error);
    }
};

const nextQuestion = async () => {
    if (currentQuestionIndex.value < questions.value.length - 1) {
        isLoading.value = true;
        await saveProgress();
        currentQuestionIndex.value++;
        isLoading.value = false;
    }
};

const previousQuestion = async () => {
    if (currentQuestionIndex.value > 0) {
        isLoading.value = true;
        await saveProgress();
        currentQuestionIndex.value--;
        isLoading.value = false;
    }
};

const navigateToQuestion = async (index) => {
    if (
        index >= 0 &&
        index < questions.value.length &&
        index !== currentQuestionIndex.value
    ) {
        isLoading.value = true;
        await saveProgress();
        currentQuestionIndex.value = index;
        isLoading.value = false;
    }
};

const submitTest = async () => {
    if (isSubmitting.value) return;

    isSubmitting.value = true;

    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }

    await saveProgress();

    const answers = {};
    questions.value.forEach((question) => {
        answers[question.id] = question.selectedAnswer || "";
    });

    router.post("/submit-test", { answers });
};

const startTimer = () => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
    timerInterval.value = setInterval(() => {
        timeRemaining.value--;
        if (timeRemaining.value <= 0) {
            handleTimeUp();
        }
    }, 1000);
};

const handleTimeUp = () => {
    clearInterval(timerInterval.value);
    submitTest();
};

onMounted(() => {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement("meta");
        meta.name = "csrf-token";
        meta.content =
            document.querySelector('input[name="_token"]')?.value || "";
        document.head.appendChild(meta);
    }

    setTimeout(() => {
        startTimer();
        saveProgress().then(() => {
            isPageLoading.value = false;
        });
    }, 300);
});

onUnmounted(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
});
</script>
