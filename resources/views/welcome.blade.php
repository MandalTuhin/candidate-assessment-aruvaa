<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Technical Assessment</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
                Select Programming Languages
            </h1>

            <form action="{{ route('test.start') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    @if ($errors->any())
                    <div
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
                    >
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif @foreach($languages as $language)
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="languages[]"
                            value="{{ $language->id }}"
                            id="lang-{{ $language->id }}"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label
                            for="lang-{{ $language->id }}"
                            class="ml-3 text-gray-700 font-medium cursor-pointer"
                        >
                            {{ $language->name }}
                        </label>
                    </div>
                    @endforeach

                    <div class="mb-6 space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Full Name</label
                            >
                            <input
                                type="text"
                                name="name"
                                required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Email Address</label
                            >
                            <input
                                type="email"
                                name="email"
                                required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
                            />
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200"
                >
                    Start Test
                </button>
            </form>
        </div>
    </body>
</html>
