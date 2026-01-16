<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Technical Test</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 p-4 md:p-8" x-data="assessmentData()" x-init="init()">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Technical Assessment
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Question <span x-text="currentQuestionIndex + 1"></span> of <span x-text="totalQuestions"></span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-600">
                        Candidate: {{ session("candidate_name") }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ session("candidate_email") }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-medium text-gray-700" x-text="Math.round((currentQuestionIndex + 1) / totalQuestions * 100) + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div 
                        class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                        :style="'width: ' + ((currentQuestionIndex + 1) / totalQuestions * 100) + '%'"
                    ></div>
                </div>
            </div>

            <!-- Timer -->
            <div class="mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Time Remaining:</span>
                    <span 
                        class="text-lg font-bold"
                        :class="{
                            'text-red-600': timeRemaining <= 10,
                            'text-yellow-600': timeRemaining <= 30 && timeRemaining > 10,
                            'text-green-600': timeRemaining > 30
                        }"
                        x-text="formatTime(timeRemaining)"
                    ></span>
                </div>
            </div>

            <!-- Current Score Display (Optional) -->
            <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Current Score:</span>
                    <span class="text-lg font-bold text-blue-600" x-text="calculateCurrentScore() + '%'"></span>
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
                        class="mb-8 p-4 border-l-4 border-blue-500 bg-gray-50"
                        x-show="currentQuestionIndex === index"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <p class="font-bold text-lg flex-1">
                                <span x-text="'Q' + (index + 1) + ':'"></span>
                                <span x-text="question.question_text"></span>
                            </p>
                        </div>
                        <span class="text-sm text-blue-600 font-mono" x-text="'[' + question.language_name + ']'"></span>

                        <div class="mt-4 space-y-2">
                            <template x-for="(option, optIndex) in question.options" :key="optIndex">
                                <label
                                    class="flex items-center space-x-3 p-2 border rounded hover:bg-gray-100 cursor-pointer transition-colors"
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
                                        class="h-4 w-4"
                                    />
                                    <span x-text="option"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t">
                    <button
                        type="button"
                        @click="previousQuestion()"
                        :disabled="currentQuestionIndex === 0"
                        class="px-6 py-2 rounded font-bold transition-colors"
                        :class="{
                            'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === 0,
                            'bg-gray-600 text-white hover:bg-gray-700': currentQuestionIndex > 0
                        }"
                    >
                        ← Previous
                    </button>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="submitTest()"
                            class="bg-red-600 text-white px-6 py-2 rounded font-bold hover:bg-red-700 transition-colors"
                        >
                            Submit Test
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="nextQuestion()"
                        :disabled="currentQuestionIndex === questions.length - 1"
                        class="px-6 py-2 rounded font-bold transition-colors"
                        :class="{
                            'bg-gray-300 text-gray-500 cursor-not-allowed': currentQuestionIndex === questions.length - 1,
                            'bg-blue-600 text-white hover:bg-blue-700': currentQuestionIndex < questions.length - 1
                        }"
                    >
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <script>
            function assessmentData() {
                return {
                    questions: @json($questionsData),
                    currentQuestionIndex: 0,
                    timeRemaining: 60, // 60 seconds per question
                    timerInterval: null,
                    timeLimit: 60,

                    init() {
                        // Initialize questions with selectedAnswer property
                        this.questions = this.questions.map(q => ({
                            ...q,
                            selectedAnswer: null
                        }));
                        this.timeRemaining = this.timeLimit;
                        this.startTimer();
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
                    },

                    nextQuestion() {
                        if (this.currentQuestionIndex < this.questions.length - 1) {
                            this.currentQuestionIndex++;
                            this.resetTimer();
                        }
                    },

                    previousQuestion() {
                        if (this.currentQuestionIndex > 0) {
                            this.currentQuestionIndex--;
                            this.resetTimer();
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

                    resetTimer() {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                        }
                        this.timeRemaining = this.timeLimit;
                        this.startTimer();
                    },

                    handleTimeUp() {
                        clearInterval(this.timerInterval);
                        // Auto-advance to next question or submit if last question
                        if (this.currentQuestionIndex < this.questions.length - 1) {
                            this.nextQuestion();
                        } else {
                            this.submitTest();
                        }
                    },

                    formatTime(seconds) {
                        const mins = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        return `${mins}:${secs.toString().padStart(2, '0')}`;
                    },

                    calculateCurrentScore() {
                        if (this.questions.length === 0) return 0;
                        const answered = this.questions.filter(q => q.selectedAnswer !== null);
                        if (answered.length === 0) return 0;
                        
                        const correct = answered.filter(q => q.selectedAnswer === q.correct_answer).length;
                        return Math.round((correct / answered.length) * 100);
                    },

                    submitTest() {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                        }
                        // Update all hidden inputs before submit
                        this.questions.forEach(question => {
                            const hiddenInput = document.getElementById('answer_' + question.id);
                            if (hiddenInput) {
                                hiddenInput.value = question.selectedAnswer || '';
                            }
                        });
                        document.getElementById('assessmentForm').submit();
                    }
                }
            }
        </script>
    </body>
</html>
