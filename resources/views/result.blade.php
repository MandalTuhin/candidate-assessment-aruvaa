<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Assessment Result</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center p-3 sm:p-4" x-data="{ reviewOpen: false }">
        <div
            class="bg-white p-4 sm:p-6 md:p-8 rounded-lg shadow-lg w-full max-w-2xl text-center"
        >
            @if ($errors->any())
            <div
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
            >
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif @if(session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                {{ session("success") }}
            </div>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Assessment Result</h1>
            <p class="text-sm sm:text-base text-gray-600 mb-4 break-words">
                Prepared for: <strong>{{ session("candidate_name") }}</strong>
            </p>

            <div
                class="text-5xl sm:text-6xl font-extrabold mb-4 sm:mb-6 {{
                    $passed ? 'text-green-600' : 'text-red-600'
                }}"
            >
                {{ $score }}%
            </div>

            <!-- Score Analytics -->
            @if(!empty($analytics))
            <div class="mb-6 sm:mb-8 bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-200 text-left">
                <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-gray-800">Score Analytics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                    <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $analytics['total_questions'] ?? 0 }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Total Questions</div>
                    </div>
                    <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $analytics['correct'] ?? 0 }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Correct</div>
                    </div>
                    <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl sm:text-3xl font-bold text-red-600">{{ $analytics['incorrect'] ?? 0 }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Incorrect</div>
                    </div>
                    <div class="text-center p-3 sm:p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $analytics['skipped'] ?? 0 }}</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Skipped</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-center">
                        <span class="text-sm text-gray-600">Questions Answered: </span>
                        <span class="text-lg font-bold text-gray-800">{{ $analytics['answered'] ?? 0 }} / {{ $analytics['total_questions'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Review All Questions -->
            @if(!empty($allQuestionsReview) && count($allQuestionsReview) > 0)
            <div class="mb-6 sm:mb-8 bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden">
                <button
                    @click="reviewOpen = !reviewOpen"
                    class="w-full flex items-center justify-between p-4 sm:p-5 text-left bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-all touch-manipulation border-b border-gray-200"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800">Review All Questions</h2>
                            <p class="text-xs sm:text-sm text-gray-600 mt-0.5">View your answers and correct solutions</p>
                        </div>
                    </div>
                    <svg
                        class="w-6 h-6 text-gray-600 transition-transform duration-200"
                        :class="{ 'rotate-180': reviewOpen }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div
                    x-show="reviewOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="p-4 sm:p-5 bg-gray-50"
                    style="display: none;"
                >
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($allQuestionsReview as $question)
                        <div class="bg-white rounded-lg border-l-4 shadow-sm overflow-hidden
                            {{ $question['status'] === 'correct' ? 'border-green-500' : ($question['status'] === 'incorrect' ? 'border-red-500' : 'border-yellow-500') }}">
                            <div class="p-3 sm:p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                                {{ $question['status'] === 'correct' ? 'bg-green-100 text-green-700' : ($question['status'] === 'incorrect' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ $question['question_number'] }}
                                            </span>
                                            <span class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                                [{{ $question['language_name'] }}]
                                            </span>
                                        </div>
                                        <p class="font-semibold text-sm sm:text-base text-gray-800 break-words mt-4">
                                            {{ $question['question_text'] }}
                                        </p>
                                    </div>
                                    <div class="ml-3">
                                        @if($question['status'] === 'correct')
                                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($question['status'] === 'incorrect')
                                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @if($question['status'] === 'skipped')
                                        <div class="p-2.5 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                            <span class="text-xs sm:text-sm font-medium text-yellow-800">
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Question Skipped - No answer provided
                                            </span>
                                        </div>
                                    @else
                                        <div class="p-2.5 sm:p-3 rounded-md {{ $question['status'] === 'correct' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                            <span class="text-xs sm:text-sm font-medium {{ $question['status'] === 'correct' ? 'text-green-700' : 'text-red-700' }}">Your Answer:</span>
                                            <span class="ml-2 text-sm sm:text-base font-semibold {{ $question['status'] === 'correct' ? 'text-green-800' : 'text-red-800' }}">
                                                {{ $question['user_answer'] }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($question['status'] !== 'correct')
                                        <div class="p-2.5 sm:p-3 bg-green-50 border border-green-200 rounded-md">
                                            <span class="text-xs sm:text-sm font-medium text-green-700">Correct Answer:</span>
                                            <span class="ml-2 text-sm sm:text-base font-semibold text-green-800">
                                                {{ $question['correct_answer'] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($passed)
            <div
                class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 text-left"
            >
                <p class="text-green-700 font-bold">
                    Congratulations! You passed the assessment.
                </p>
                <p class="text-green-600 text-sm">
                    Please upload your resume to complete the application.
                </p>
            </div>

            <form
                action="{{ route('resume.upload') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-6"
            >
                @csrf
                <input
                    type="hidden"
                    name="assessment_id"
                    value="{{ $assessmentId }}"
                />

                <div class="mb-4">
                    <input
                        type="file"
                        name="resume"
                        accept=".pdf,.doc,.docx"
                        required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                    />
                    <p class="text-xs text-gray-400 mt-2">
                        Accepted formats: PDF, DOC, DOCX (Max 2MB)
                    </p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700"
                >
                    Upload Resume
                </button>
            </form>
            @else
            <div class="bg-red-50 border-l-4 border-red-400 p-4 text-left">
                <p class="text-red-700 font-bold">
                    Thank you for your interest.
                </p>
                <p class="text-red-600 text-sm">
                    Unfortunately, your score did not meet the required
                    threshold. Please try again later.
                </p>
            </div>

            <a
                href="{{ route('home') }}"
                class="mt-6 inline-block text-blue-600 hover:underline"
                >Return to Home</a
            >
            @endif
        </div>
    </body>
</html>
