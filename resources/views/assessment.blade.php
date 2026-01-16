<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Technical Test</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 p-4 md:p-8" x-data="assessmentData()" x-init="init()">
        <!-- Loading Overlay -->
        <div 
            x-show="isPageLoading" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
        >
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg flex items-center gap-3 mx-4">
                <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-base sm:text-lg font-medium text-gray-700">Loading assessment...</span>
            </div>
        </div>

        <div class="max-w-3xl mx-auto bg-white p-4 sm:p-6 rounded shadow" :class="{ 'opacity-50 pointer-events-none': isPageLoading }">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 sm:mb-6 border-b pb-3 sm:pb-4 gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                        Technical Assessment
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                        Question <span x-text="currentQuestionIndex + 1"></span> of <span x-text="totalQuestions"></span>
                    </p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">
                        Candidate: {{ session("candidate_name") }}
                    </p>
                    <p class="text-xs text-gray-400 break-all">
                        {{ session("candidate_email") }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4 sm:mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs sm:text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-xs sm:text-sm font-medium text-gray-700" x-text="Math.round((currentQuestionIndex + 1) / totalQuestions * 100) + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5">
                    <div 
                        class="bg-blue-600 h-2 sm:h-2.5 rounded-full transition-all duration-300"
                        :style="'width: ' + ((currentQuestionIndex + 1) / totalQuestions * 100) + '%'"
                    ></div>
                </div>
            </div>

            <!-- Question MiniMap -->
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs sm:text-sm font-medium text-gray-700">Question Navigator</span>
                    <span class="text-xs text-gray-500">Click to jump to question</span>
                </div>
                <div class="grid grid-cols-5 sm:grid-cols-10 gap-2">
                    <template x-for="(question, index) in questions" :key="question.id">
                        <button
                            type="button"
                            @click="navigateToQuestion(index)"
                            class="aspect-square rounded-lg font-bold text-xs sm:text-sm transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-400 touch-manipulation"
                            :class="{
                                'bg-blue-600 text-white shadow-md': currentQuestionIndex === index,
                                'bg-green-500 text-white': currentQuestionIndex !== index && question.selectedAnswer,
                                'bg-gray-200 text-gray-600 hover:bg-gray-300': currentQuestionIndex !== index && !question.selectedAnswer
                            }"
                            :title="'Question ' + (index + 1) + (question.selectedAnswer ? ' (Answered)' : ' (Not answered)')"
                            x-text="index + 1"
                        ></button>
                    </template>
                </div>
                <div class="flex flex-wrap gap-3 sm:gap-4 mt-3 text-xs">
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 bg-blue-600 rounded"></div>
                        <span class="text-gray-600">Current</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-gray-600">Answered</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 bg-gray-200 rounded"></div>
                        <span class="text-gray-600">Not Answered</span>
                    </div>
                </div>
            </div>

            <!-- Timer -->
            <div class="mb-4 sm:mb-6 p-2 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-xs sm:text-sm font-medium text-gray-700">Time Remaining:</span>
                    <span 
                        class="text-base sm:text-lg font-bold"
                        :class="{
                            'text-red-600': timeRemaining <= 60,
                            'text-yellow-600': timeRemaining <= 120 && timeRemaining > 60,
                            'text-green-600': timeRemaining > 120
                        }"
                        x-text="formatTime(timeRemaining)"
                    ></span>
                </div>
            </div>

            <!-- Question Form -->
            <form action="{{ route('test.submit') }}" method="POST" id="assessmentForm">
                @csrf
                
                <!-- Hidden inputs for all answers -->
                <template x-for="(question, index) in questions" :key="question.id">
                    <input 
                        type="hidden" 
                        :name="'answers[' + question.id + ']'" 
                        :value="question.selectedAnswer || ''"
                        :id="'answer_' + question.id"
                    />
                </template>

                <!-- Current Question Display -->
                <template x-for="(question, index) in questions" :key="question.id">
                    <div 
                        class="mb-6 sm:mb-8 p-3 sm:p-4 border-l-4 border-blue-500 bg-gray-50"
                        x-show="currentQuestionIndex === index"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <p class="font-bold text-base sm:text-lg flex-1 wrap-break-word">
                                <span x-text="'Q' + (index + 1) + ':'"></span>
                                <span x-text="question.question_text"></span>
                            </p>
                        </div>
                        <span class="text-xs sm:text-sm text-blue-600 font-mono" x-text="'[' + question.language_name + ']'"></span>

                        <div class="mt-3 sm:mt-4 space-y-2">
                            <template x-for="(option, optIndex) in question.options" :key="optIndex">
                                <label
                                    class="flex items-center space-x-2 sm:space-x-3 p-3 sm:p-2 border rounded hover:bg-gray-100 cursor-pointer transition-colors touch-manipulation"
                                    :class="{
                                        'bg-blue-100 border-blue-400': question.selectedAnswer === option
                                    }"
                                >
                                    <input
                                        type="radio"
                                        :name="'current_answer_' + question.id"
                                        :value="option"
                                        :checked="question.selectedAnswer === option"
                                        @change="selectAnswer(question, option)"
                                        class="h-5 w-5 sm:h-4 sm:w-4 shrink-0"
                                    />
                                    <span class="text-sm sm:text-base wrap-break-word" x-text="option"></span>
                                </label>
                            </template>
                        </div>

                        <!-- Clear Response Button -->
                        <div class="mt-3 sm:mt-4" x-show="question.selectedAnswer">
                            <button
                                type="button"
                                @click="clearAnswer(question)"
                                class="text-xs sm:text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1.5 transition-colors touch-manipulation"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Response
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-0 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t">
                    <button
                        type="button"
                        @click="previousQuestion()"
                        :disabled="currentQuestionIndex === 0 || isLoading"
                        class="px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
                        :class="{
                            'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === 0 || isLoading,
                            'bg-gray-600 text-white hover:bg-gray-700': currentQuestionIndex > 0 && !isLoading
                        }"
                    >
                        <svg x-show="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!isLoading" class="text-sm sm:text-base">← Previous</span>
                        <span x-show="isLoading" class="text-sm sm:text-base">Loading...</span>
                    </button>

                    <button
                        type="button"
                        @click="submitTest()"
                        :disabled="isSubmitting"
                        class="bg-red-600 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation order-first sm:order-0"
                    >
                        <svg x-show="isSubmitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!isSubmitting" class="text-sm sm:text-base">Submit Test</span>
                        <span x-show="isSubmitting" class="text-sm sm:text-base">Submitting...</span>
                    </button>

                    <button
                        type="button"
                        @click="nextQuestion()"
                        :disabled="currentQuestionIndex === questions.length - 1 || isLoading"
                        class="px-4 sm:px-6 py-2.5 sm:py-2 rounded font-bold transition-colors flex items-center justify-center gap-2 touch-manipulation"
                        :class="{
                            'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === questions.length - 1 || isLoading,
                            'bg-blue-600 text-white hover:bg-blue-700': currentQuestionIndex < questions.length - 1 && !isLoading
                        }"
                    >
                        <span x-show="!isLoading" class="text-sm sm:text-base">Next →</span>
                        <span x-show="isLoading" class="text-sm sm:text-base">Loading...</span>
                        <svg x-show="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <script>
            function assessmentData() {
                return {
                    questions: @json($questionsData),
                    currentQuestionIndex: 0,
                    timeRemaining: 300, // 5 minutes (300 seconds) total for entire test
                    timerInterval: null,
                    timeLimit: 300, // 5 minutes total
                    isLoading: false,
                    isSubmitting: false,
                    isPageLoading: true,

                    init() {
                        // Show loading overlay initially
                        this.isPageLoading = true;
                        
                        // Simulate initial load time (or remove if not needed)
                        setTimeout(() => {
                            // Questions already have selectedAnswer from saved progress (if any)
                            // Start global timer (doesn't reset between questions)
                            this.startTimer();
                            // Save initial progress
                            this.saveProgress().then(() => {
                                this.isPageLoading = false;
                            });
                        }, 300);
                    },

                    get currentQuestion() {
                        return this.questions[this.currentQuestionIndex];
                    },

                    get totalQuestions() {
                        return this.questions.length;
                    },

                    selectAnswer(question, answer) {
                        question.selectedAnswer = answer;
                        // Update hidden input
                        const hiddenInput = document.getElementById('answer_' + question.id);
                        if (hiddenInput) {
                            hiddenInput.value = answer;
                        }
                        // Auto-save progress when answer is selected
                        this.saveProgress();
                    },

                    clearAnswer(question) {
                        question.selectedAnswer = null;
                        // Update hidden input
                        const hiddenInput = document.getElementById('answer_' + question.id);
                        if (hiddenInput) {
                            hiddenInput.value = '';
                        }
                        // Uncheck all radio buttons for this question
                        const radios = document.querySelectorAll(`input[name="current_answer_${question.id}"]`);
                        radios.forEach(radio => {
                            radio.checked = false;
                        });
                        // Auto-save progress when answer is cleared
                        this.saveProgress();
                    },

                    async nextQuestion() {
                        if (this.currentQuestionIndex < this.questions.length - 1) {
                            this.isLoading = true;
                            // Save progress before navigating
                            await this.saveProgress();
                            this.currentQuestionIndex++;
                            this.isLoading = false;
                            // Timer continues running - no reset
                        }
                    },

                    async previousQuestion() {
                        if (this.currentQuestionIndex > 0) {
                            this.isLoading = true;
                            // Save progress before navigating
                            await this.saveProgress();
                            this.currentQuestionIndex--;
                            this.isLoading = false;
                            // Timer continues running - no reset
                        }
                    },

                    async navigateToQuestion(index) {
                        if (index >= 0 && index < this.questions.length && index !== this.currentQuestionIndex) {
                            this.isLoading = true;
                            // Save progress before navigating
                            await this.saveProgress();
                            this.currentQuestionIndex = index;
                            this.isLoading = false;
                            // Timer continues running - no reset
                        }
                    },

                    async saveProgress() {
                        try {
                            const answers = {};
                            this.questions.forEach(q => {
                                if (q.selectedAnswer) {
                                    answers[q.id] = q.selectedAnswer;
                                }
                            });

                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                             document.querySelector('input[name="_token"]')?.value;

                            const response = await fetch('{{ route("progress.save") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ answers })
                            });

                            if (!response.ok) {
                                throw new Error('Failed to save progress');
                            }

                            return await response.json();
                        } catch (error) {
                            console.error('Failed to save progress:', error);
                            // Don't throw - allow navigation to continue even if save fails
                            return { success: false };
                        }
                    },

                    startTimer() {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                        }
                        this.timerInterval = setInterval(() => {
                            this.timeRemaining--;
                            if (this.timeRemaining <= 0) {
                                this.handleTimeUp();
                            }
                        }, 1000);
                    },

                    handleTimeUp() {
                        clearInterval(this.timerInterval);
                        // Auto-submit when global timer runs out
                        this.submitTest();
                    },

                    formatTime(seconds) {
                        const mins = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        return `${mins}:${secs.toString().padStart(2, '0')}`;
                    },

                    async submitTest() {
                        if (this.isSubmitting) {
                            return; // Prevent double submission
                        }

                        this.isSubmitting = true;

                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                        }

                        // Save final progress before submitting
                        await this.saveProgress();

                        // Update all hidden inputs before submit
                        this.questions.forEach(question => {
                            const hiddenInput = document.getElementById('answer_' + question.id);
                            if (hiddenInput) {
                                hiddenInput.value = question.selectedAnswer || '';
                            }
                        });

                        // Submit the form
                        document.getElementById('assessmentForm').submit();
                    }
                }
            }
        </script>
    </body>
</html>
