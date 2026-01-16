<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Technical Test</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 p-8">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Technical Assessment
                </h1>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-600">
                        Candidate: {{ session("candidate_name") }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ session("candidate_email") }}
                    </p>
                </div>
            </div>
            <form action="{{ route('test.submit') }}" method="POST">
                @csrf @foreach($questions as $index => $question)
                <div class="mb-8 p-4 border-l-4 border-blue-500 bg-gray-50">
                    <p class="font-bold text-lg">
                        Q{{ $index + 1 }}: {{ $question->question_text }}
                    </p>
                    <span class="text-sm text-blue-600 font-mono"
                        >[{{ $question->language->name }}]</span
                    >

                    <div class="mt-4 space-y-2">
                        @foreach($question->options as $option)
                        <label
                            class="flex items-center space-x-3 p-2 border rounded hover:bg-gray-100 cursor-pointer"
                        >
                            <input
                                type="radio"
                                name="answers[{{ $question->id }}]"
                                value="{{ $option }}"
                                class="h-4 w-4"
                            />
                            <span>{{ $option }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <button
                    type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded font-bold hover:bg-green-700"
                >
                    Submit Test
                </button>
            </form>
        </div>
    </body>
</html>
