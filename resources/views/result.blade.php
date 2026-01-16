<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Assessment Result</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
        <div
            class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl text-center"
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
            <h1 class="text-3xl font-bold mb-2">Assessment Result</h1>
            <p class="text-gray-600 mb-4">
                Prepared for: <strong>{{ session("candidate_name") }}</strong>
            </p>

            <div
                class="text-6xl font-extrabold mb-6 {{
                    $passed ? 'text-green-600' : 'text-red-600'
                }}"
            >
                {{ $score }}%
            </div>

            <!-- Score Analytics -->
            @if(!empty($analytics))
            <div class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Score Analytics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-blue-600">{{ $analytics['total_questions'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Questions</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-green-600">{{ $analytics['correct'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Correct</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-red-600">{{ $analytics['incorrect'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Incorrect</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-yellow-600">{{ $analytics['skipped'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Skipped</div>
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

            <!-- Review Wrong Answers -->
            @if(!empty($wrongAnswers) && count($wrongAnswers) > 0)
            <div class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200 text-left">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Review Wrong Answers</h2>
                <div class="space-y-6">
                    @foreach($wrongAnswers as $index => $wrongAnswer)
                    <div class="bg-white p-4 rounded-lg border-l-4 border-red-500 shadow-sm">
                        <div class="flex items-start justify-between mb-2">
                            <p class="font-bold text-lg text-gray-800 flex-1">
                                Question {{ $index + 1 }}: {{ $wrongAnswer['question_text'] }}
                            </p>
                        </div>
                        <span class="text-sm text-blue-600 font-mono mb-3 inline-block">
                            [{{ $wrongAnswer['language_name'] }}]
                        </span>
                        
                        <div class="mt-3 space-y-2">
                            <div class="p-3 bg-red-50 border border-red-200 rounded">
                                <span class="text-sm font-medium text-red-700">Your Answer:</span>
                                <span class="ml-2 text-red-800 font-semibold">{{ $wrongAnswer['user_answer'] }}</span>
                            </div>
                            <div class="p-3 bg-green-50 border border-green-200 rounded">
                                <span class="text-sm font-medium text-green-700">Correct Answer:</span>
                                <span class="ml-2 text-green-800 font-semibold">{{ $wrongAnswer['correct_answer'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
