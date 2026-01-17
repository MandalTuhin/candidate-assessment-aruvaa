<template>
    <section class="mb-6 space-y-4 sm:space-y-5" aria-label="Candidate information">
        <div class="space-y-2">
            <label for="candidate-name" class="block text-sm font-medium text-gray-900">
                Full Name
            </label>
            <input
                id="candidate-name"
                type="text"
                :value="name"
                @input="$emit('update:name', $event.target.value)"
                required
                class="block w-full rounded-lg border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                aria-describedby="name-help"
                autocomplete="name"
                placeholder="Enter your full name"
            />
            <span id="name-help" class="sr-only">Enter your full name as it appears on official documents</span>
        </div>
        <div class="space-y-2">
            <label for="candidate-email" class="block text-sm font-medium text-gray-900">
                Email Address
            </label>
            <input
                id="candidate-email"
                type="email"
                :value="email"
                @input="$emit('update:email', $event.target.value)"
                required
                class="block w-full rounded-lg border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                aria-describedby="email-help"
                autocomplete="email"
                placeholder="Enter your email address"
            />
            <span id="email-help" class="sr-only">Enter a valid email address for receiving assessment results</span>
        </div>
    </section>
</template>

<script setup>
defineProps({
    name: String,
    email: String,
});

defineEmits(["update:name", "update:email"]);
</script>
