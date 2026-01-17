<template>
    <main class="min-h-screen bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
        <LoadingOverlay
            :is-loading="isPageLoading"
            message="Loading assessment..."
        />

        <section
            class="mx-auto max-w-4xl rounded-xl bg-white shadow-lg border border-gray-200 p-4 sm:p-6 lg:p-8"
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

            <!-- Progress Save Indicator -->
            <div 
                v-if="isSaving" 
                class="mb-4 flex items-center justify-center gap-2 rounded-lg bg-blue-50 p-2 text-sm text-blue-700"
                role="status"
                aria-live="polite"
            >
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving progress...
            </div>

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
const autoSaveInterval = ref(null);
const isLoading = ref(false);
const isSubmitting = ref(false);
const isPageLoading = ref(true);
const isSaving = ref(false);

const totalQuestions = computed(() => questions.value.length);
const answeredCount = computed(
    () => questions.value.filter((q) => q.selectedAnswer).length,
);

// Debounce function to prevent too many save requests
let saveTimeout = null;

const debouncedSaveProgress = () => {
    if (saveTimeout) {
        clearTimeout(saveTimeout);
    }
    saveTimeout = setTimeout(() => {
        saveProgress();
    }, 500); // Wait 500ms after last change before saving
};

const selectAnswer = (question, answer) => {
    question.selectedAnswer = answer;
    debouncedSaveProgress();
};

const clearAnswer = (question) => {
    question.selectedAnswer = null;
    debouncedSaveProgress();
};

const getCsrfToken = () => {
    // Try to get CSRF token from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (metaToken) return metaToken;
    
    // Fallback: try to get from hidden input (if exists)
    const inputToken = document.querySelector('input[name="_token"]')?.value;
    if (inputToken) return inputToken;
    
    // Last resort: try to get from cookie (if using cookie-based CSRF)
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'XSRF-TOKEN') {
            return decodeURIComponent(value);
        }
    }
    
    console.warn('CSRF token not found');
    return '';
};

const saveProgress = async () => {
    try {
        isSaving.value = true;
        
        const answers = {};
        questions.value.forEach((q) => {
            if (q.selectedAnswer) {
                answers[q.id] = q.selectedAnswer;
            }
        });

        console.log('Saving progress:', answers); // Debug log

        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            console.error('CSRF token not available');
            return;
        }

        const response = await fetch("/save-progress", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json",
            },
            body: JSON.stringify({ answers }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error("Save progress failed:", errorData);
            throw new Error(`HTTP ${response.status}: ${errorData.message || 'Unknown error'}`);
        }

        const result = await response.json();
        console.log('Progress saved successfully:', result); // Debug log
    } catch (error) {
        console.error("Failed to save progress:", error);
        // Don't throw the error to prevent disrupting the user experience
        // but log it for debugging
    } finally {
        isSaving.value = false;
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

const startAutoSave = () => {
    // Auto-save every 30 seconds
    if (autoSaveInterval.value) {
        clearInterval(autoSaveInterval.value);
    }
    autoSaveInterval.value = setInterval(() => {
        saveProgress();
    }, 30000); // 30 seconds
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
    // Ensure CSRF token is available
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement("meta");
        meta.name = "csrf-token";
        meta.content = document.querySelector('input[name="_token"]')?.value || "";
        document.head.appendChild(meta);
    }

    // Add beforeunload listener to save progress when leaving
    const handleBeforeUnload = (event) => {
        // Save progress synchronously before leaving
        const answers = {};
        questions.value.forEach((q) => {
            if (q.selectedAnswer) {
                answers[q.id] = q.selectedAnswer;
            }
        });

        if (Object.keys(answers).length > 0) {
            // Use sendBeacon for reliable delivery when page is unloading
            const csrfToken = getCsrfToken();
            if (csrfToken && navigator.sendBeacon) {
                const formData = new FormData();
                formData.append('answers', JSON.stringify(answers));
                formData.append('_token', csrfToken);
                navigator.sendBeacon('/save-progress', formData);
            }
        }
    };

    window.addEventListener('beforeunload', handleBeforeUnload);

    // Initialize the assessment
    setTimeout(() => {
        startTimer();
        startAutoSave(); // Start periodic auto-save
        // Save initial progress to establish session
        saveProgress().then(() => {
            isPageLoading.value = false;
        });
    }, 300);

    // Store the cleanup function
    window.assessmentCleanup = () => {
        window.removeEventListener('beforeunload', handleBeforeUnload);
    };
});

onUnmounted(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
    if (autoSaveInterval.value) {
        clearInterval(autoSaveInterval.value);
    }
    if (saveTimeout) {
        clearTimeout(saveTimeout);
    }
    
    // Clean up beforeunload listener
    if (window.assessmentCleanup) {
        window.assessmentCleanup();
        delete window.assessmentCleanup;
    }
});
</script>
